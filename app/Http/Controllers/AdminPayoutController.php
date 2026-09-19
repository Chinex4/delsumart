<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPayoutController extends Controller
{
    public function index(Request $request)
    {
        $query = PayoutRequest::with(['user','bankAccount','processor']);
        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        return view('admin.payouts', [
            'payouts' => $query->latest()->paginate(20)->withQueryString(),
            'stats' => [
                'pending' => PayoutRequest::where('status','pending')->count(),
                'processing' => PayoutRequest::where('status','processing')->count(),
                'paid_amount' => PayoutRequest::where('status','paid')->sum('amount'),
            ],
        ]);
    }

    public function update(Request $request, PayoutRequest $payout)
    {
        $data = $request->validate([
            'status' => 'required|in:processing,paid,rejected',
            'admin_note' => 'nullable|required_if:status,rejected|string|max:1000',
        ]);
        abort_unless(in_array($payout->status, ['pending','processing'], true), 409, 'This payout is already final.');

        DB::transaction(function () use ($request, $payout, $data) {
            $payout->update([
                'status' => $data['status'], 'admin_note' => $data['admin_note'] ?? null,
                'processed_by' => $request->user()->id,
                'processed_at' => in_array($data['status'], ['paid','rejected'], true) ? now() : null,
            ]);
            AuditLog::create([
                'admin_id' => $request->user()->id, 'action_type' => 'payout_'.$data['status'],
                'target_type' => 'payout_request', 'target_id' => $payout->id,
                'notes' => 'Payout '.$payout->reference.' for ₦'.number_format((float)$payout->amount, 2).'. '.($data['admin_note'] ?? ''),
            ]);
        });

        return back()->with('success', 'Payout request updated and audited.');
    }
}
