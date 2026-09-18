<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dispute;
use App\Notifications\TransactionUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDisputeController extends Controller
{
    public function index()
    {
        return view('admin.disputes', ['disputes' => Dispute::with(['transaction.listing', 'transaction.buyer', 'transaction.seller', 'complainant'])->latest()->paginate(20)]);
    }

    public function resolve(Request $r, Dispute $dispute)
    {
        $d = $r->validate(['decision' => 'required|in:resolved,rejected', 'resolution' => 'required|string|max:2000', 'transaction_action' => 'required|in:release,refund,hold']);
        DB::transaction(function () use ($r, $dispute, $d) {
            $dispute->update(['status' => $d['decision'], 'resolution' => $d['resolution'], 'resolved_by' => $r->user()->id, 'resolved_at' => now()]);
            $tx = $dispute->transaction()->lockForUpdate()->first();
            if ($d['transaction_action'] === 'release') {
                $tx->update(['status' => 'released', 'completed_at' => now()]);
            } elseif ($d['transaction_action'] === 'refund') {
                $tx->update(['status' => 'refunded']);
            } else {
                $tx->update(['status' => 'paid_held']);
            }AuditLog::create(['admin_id' => $r->user()->id, 'action_type' => 'dispute_'.$d['decision'], 'target_type' => 'dispute', 'target_id' => $dispute->id, 'notes' => $d['resolution'].'; transaction action='.$d['transaction_action']]);
            $tx->buyer->notify(new TransactionUpdateNotification($tx, 'An administrator reviewed a dispute on your transaction.'));
            $tx->seller->notify(new TransactionUpdateNotification($tx, 'An administrator reviewed a dispute on your transaction.'));
        }
        );

        return back()->with('success', 'Dispute decision recorded and audited.');
    }
}
