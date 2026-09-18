<?php
namespace App\Http\Controllers;
use App\Models\Dispute; use Illuminate\Http\Request; use Illuminate\Support\Facades\Storage;
class PrivateEvidenceController extends Controller {
 public function show(Request $request,Dispute $dispute){
  $tx=$dispute->transaction;
  abort_unless($request->user()->isAdmin()||in_array($request->user()->id,[$tx->buyer_id,$tx->seller_id],true),403);
  abort_unless($dispute->evidence_path&&Storage::disk('local')->exists($dispute->evidence_path),404);
  return Storage::disk('local')->response($dispute->evidence_path);
 }
}