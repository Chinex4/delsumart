<?php

namespace App\Http\Controllers;

use App\Services\FraudScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function show(Request $r)
    {
        return view('kyc.show', ['verification' => $r->user()->verification]);
    }

    public function store(Request $r, FraudScoringService $fraud)
    {
        $u = $r->user();
        $d = $r->validate(['id_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096', 'fee_receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096']);
        $existing = $u->verification;
        if ($existing?->verification_status === 'verified') {
            abort(409);
        }
        $id = $r->file('id_card')->store('kyc/'.$u->id, 'local');
        $fee = $r->file('fee_receipt')->store('kyc/'.$u->id, 'local');
        $payload = ['matric_no' => $u->matric_no, 'full_name' => $u->name, 'programme' => $u->programme, 'level' => $u->level, 'id_card_image' => $id, 'fee_receipt_image' => $fee, 'verification_status' => 'pending', 'verified_by' => null, 'verified_at' => null, 'rejection_reason' => null];
        if ($existing) {
            $old = [$existing->id_card_image, $existing->fee_receipt_image];
            $payload['resubmission_count'] = $existing->resubmission_count + 1;
            $existing->update($payload);
            Storage::disk('local')->delete($old);
        } else {
            $u->verification()->create($payload);
        }
        $fraud->evaluate($u->fresh());

        return redirect()->route('kyc.show')->with('success', 'Your documents were submitted securely for review.');
    }
}
