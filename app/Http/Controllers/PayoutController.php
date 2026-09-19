<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PayoutRequest;
use App\Models\Transaction;
use App\Notifications\PayoutOtpNotification;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $totalSales = (float) $user->sales()->where('status', 'released')->sum('amount');
        $reserved = (float) $user->payoutRequests()->whereIn('status', ['pending','processing','paid'])->sum('amount');
        $available = max(0, $totalSales - $reserved);

        return view('account.payouts', [
            'totalSales' => $totalSales,
            'totalPaid' => (float) $user->payoutRequests()->where('status', 'paid')->sum('amount'),
            'available' => $available,
            'bankAccount' => $user->bankAccount,
            'payouts' => $user->payoutRequests()->with('bankAccount')->latest()->paginate(10),
        ]);
    }

    public function requestBankOtp(Request $request)
    {
        $key = 'payout-bank-otp:'.$request->user()->id;
        $existing = Cache::get($key);
        if ($existing && now()->timestamp - $existing['sent_at'] < 60) {
            throw ValidationException::withMessages(['otp' => 'Please wait before requesting another code.']);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($key, ['hash' => Hash::make($code), 'sent_at' => now()->timestamp, 'attempts' => 0], now()->addMinutes(10));
        $request->user()->notify(new PayoutOtpNotification($code));

        return back()->with('success', 'A security code was sent to your email.');
    }

    public function verifyBankOtp(Request $request)
    {
        $data = $request->validate(['otp' => 'required|digits:6']);
        $key = 'payout-bank-otp:'.$request->user()->id;
        $record = Cache::get($key);

        if (! $record || $record['attempts'] >= 5 || ! Hash::check($data['otp'], $record['hash'])) {
            if ($record) {
                $record['attempts']++;
                Cache::put($key, $record, now()->addMinutes(10));
            }
            throw ValidationException::withMessages(['otp' => 'The code is invalid or expired.']);
        }

        Cache::forget($key);
        $request->session()->put('payout_bank_verified_at', now()->timestamp);

        return back()->with('success', 'Security check passed. You can now set your payout account.');
    }

    public function banks(Request $request, PaystackService $paystack)
    {
        abort_unless($this->bankSessionValid($request), 403);

        return response()->json(['data' => Cache::remember('paystack-ng-banks', now()->addHours(6), fn () => $paystack->banks())]);
    }

    public function resolveBank(Request $request, PaystackService $paystack)
    {
        abort_unless($this->bankSessionValid($request), 403);
        $data = $request->validate(['account_number' => ['required','digits:10'], 'bank_code' => 'required|string|max:20']);

        try {
            return response()->json(['data' => $paystack->resolveAccount($data['account_number'], $data['bank_code'])]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function storeBank(Request $request, PaystackService $paystack)
    {
        abort_unless($this->bankSessionValid($request), 403);
        $data = $request->validate(['account_number' => ['required','digits:10'], 'bank_code' => 'required|string|max:20', 'bank_name' => 'required|string|max:120']);

        try {
            $resolved = $paystack->resolveAccount($data['account_number'], $data['bank_code']);
            $recipient = $paystack->createTransferRecipient($resolved['account_name'], $data['account_number'], $data['bank_code']);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['account_number' => $e->getMessage()]);
        }

        $request->user()->bankAccount()->updateOrCreate([], [
            'bank_code' => $data['bank_code'], 'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'], 'account_name' => $resolved['account_name'],
            'paystack_recipient_code' => $recipient['recipient_code'] ?? null, 'verified_at' => now(),
        ]);
        $request->session()->forget('payout_bank_verified_at');

        return back()->with('success', 'Your verified payout bank account has been saved.');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:5000|max:200000']);
        $user = $request->user();
        abort_unless($user->bankAccount, 422, 'Add a verified bank account before requesting a payout.');

        DB::transaction(function () use ($user, $data) {
            $totalSales = (float) Transaction::where('seller_id', $user->id)->where('status', 'released')->lockForUpdate()->sum('amount');
            $reserved = (float) PayoutRequest::where('user_id', $user->id)->whereIn('status', ['pending','processing','paid'])->lockForUpdate()->sum('amount');
            $amount = (float) $data['amount'];
            if ($amount > max(0, $totalSales - $reserved)) {
                throw ValidationException::withMessages(['amount' => 'This amount is higher than your available payout balance.']);
            }
            PayoutRequest::create([
                'user_id' => $user->id, 'bank_account_id' => $user->bankAccount->id,
                'amount' => $amount, 'status' => 'pending', 'reference' => 'PO-'.strtoupper(Str::random(14)),
            ]);
        });

        return back()->with('success', 'Payout request submitted for administrator review.');
    }

    private function bankSessionValid(Request $request): bool
    {
        $verifiedAt = (int) $request->session()->get('payout_bank_verified_at', 0);
        return $verifiedAt > 0 && $verifiedAt >= now()->subMinutes(15)->timestamp;
    }
}
