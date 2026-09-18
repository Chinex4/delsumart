<?php
namespace App\Http\Controllers;
use App\Models\Transaction; use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class TransactionController extends Controller {public function confirm(Request $r,Transaction $transaction){abort_unless($transaction->buyer_id===$r->user()->id,403);abort_unless($transaction->status==='paid_held',409);DB::transaction(function()use($transaction){$tx=Transaction::lockForUpdate()->find($transaction->id);abort_unless($tx->status==='paid_held',409);$tx->update(['status'=>'released','completed_at'=>now()]);});return back()->with('success','Receipt confirmed. Transaction completed.');}}
