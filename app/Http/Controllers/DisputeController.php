<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Notifications\DisputeOpenedNotification;
use App\Services\FraudScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisputeController extends Controller
{
    public function store(Request $r, Transaction $transaction, FraudScoringService $fraud)
    {
        abort_unless(in_array($r->user()->id, [$transaction->buyer_id, $transaction->seller_id], true), 403);
        abort_unless(in_array($transaction->status, ['paid_held', 'release_pending'], true), 409);
        abort_if($transaction->disputes()->whereIn('status', ['open', 'under_review'])->exists(), 409, 'An active dispute already exists.');
        $d = $r->validate(['category' => 'required|string|max:80', 'details' => 'required|string|max:3000', 'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096']);
        $dispute = DB::transaction(function () use ($r, $transaction, $d) {
            $tx = Transaction::lockForUpdate()->findOrFail($transaction->id);
            abort_unless(in_array($tx->status, ['paid_held', 'release_pending'], true), 409);
            $d['complainant_id'] = $r->user()->id;
            if ($r->hasFile('evidence')) {
                $d['evidence_path'] = $r->file('evidence')->store('disputes/'.$tx->id, 'local');
            }$dispute = $tx->disputes()->create($d);
            $tx->update(['status' => 'disputed']);

            return $dispute;
        }
        );
        $other = $r->user()->id === $transaction->buyer_id ? $transaction->seller : $transaction->buyer;
        $other->notify(new DisputeOpenedNotification($dispute));
        $fraud->evaluate($transaction->buyer);
        $fraud->evaluate($transaction->seller);

        return back()->with('success','Dispute opened. Release is paused while an administrator reviews it.');
    }
}
