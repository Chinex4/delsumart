<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\PaymentEvent;
use App\Models\Transaction;
use App\Notifications\PaymentReceivedNotification;
use App\Services\FraudScoringService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController
{
    public function initialize(Request $r, Listing $listing, PaystackService $paystack)
    {
        abort_if($listing->user_id === $r->user()->id, 422, 'You cannot purchase your own listing.');
        abort_unless($listing->status === 'active', 409);
        $existing = Transaction::where('listing_id', $listing->id)->where('buyer_id', $r->user()->id)->where('status', 'pending_payment')->latest()->first();
        $tx = $existing ?: Transaction::create(['listing_id' => $listing->id, 'buyer_id' => $r->user()->id, 'seller_id' => $listing->user_id, 'amount' => $listing->price, 'status' => 'pending_payment', 'paystack_reference' => 'DM-'.Str::upper(Str::random(18))]);
        $data = $paystack->initialize($r->user()->email, (int) round(((float) $tx->amount) * 100), $tx->paystack_reference, route('payments.callback', ['reference' => $tx->paystack_reference]));

        return redirect()->away($data['authorization_url']);
    }

    public function callback(Request $r, PaystackService $paystack, FraudScoringService $fraud)
    {
        $ref = $r->string('reference')->value();
        $tx = Transaction::where('paystack_reference', $ref)->firstOrFail();
        abort_unless($tx->buyer_id === $r->user()->id, 403);
        $data = $paystack->verify($ref);
        if (($data['status'] ?? null) !== 'success' || (int) ($data['amount'] ?? 0) !== (int) round(((float) $tx->amount) * 100) || strtoupper((string) ($data['currency'] ?? 'NGN')) !== 'NGN') {
            abort(422, 'Payment could not be verified.');
        }$changed = false;
        DB::transaction(function () use ($tx, &$changed) {
            $locked = Transaction::lockForUpdate()->find($tx->id);
            if ($locked->status === 'pending_payment') {
                $locked->update(['status' => 'paid_held', 'paid_at' => now()]);
                $locked->listing()->where('status', 'active')->update(['status' => 'sold']);
                $changed = true;
            }
        });
        $tx->refresh();
        if ($changed) {
            $tx->buyer->notify(new PaymentReceivedNotification($tx, 'buyer'));
            $tx->seller->notify(new PaymentReceivedNotification($tx, 'seller'));
            $fraud->evaluate($tx->buyer);
            $fraud->evaluate($tx->seller);
        }

        return redirect()->route('dashboard')->with('success', 'Payment verified and marked as protected.');
    }

    public function webhook(Request $r, PaystackService $paystack)
    {
        $raw = $r->getContent();
        if (! $paystack->validWebhook($raw, $r->header('x-paystack-signature'))) {
            abort(401);
        }$event = $r->json()->all();
        $ref = data_get($event, 'data.reference');
        $key = hash('sha256', $raw);
        if (PaymentEvent::where('event_key', $key)->exists()) {
            return response()->json(['ok' => true]);
        }DB::transaction(function () use ($event, $ref, $key) {
            PaymentEvent::create(['event_key' => $key, 'event_type' => $event['event'] ?? 'unknown', 'reference' => $ref, 'payload' => $event, 'processed_at' => now()]);
        });

        return response()->json(['ok' => true]);
    }
}
