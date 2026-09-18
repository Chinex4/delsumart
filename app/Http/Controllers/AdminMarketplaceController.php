<?php
namespace App\Http\Controllers;
use App\Models\{Listing,Transaction}; use Illuminate\Http\Request;
class AdminMarketplaceController extends Controller {
 public function listings(Request $r){$q=Listing::with('seller');if($s=$r->string('q')->trim()->value())$q->where('title','like',"%$s%");if($status=$r->string('status')->value())$q->where('status',$status);return view('admin.listings',['listings'=>$q->latest()->paginate(20)->withQueryString()]);}
 public function transactions(Request $r){$q=Transaction::with(['listing','buyer','seller']);if($status=$r->string('status')->value())$q->where('status',$status);if($ref=$r->string('reference')->trim()->value())$q->where('paystack_reference','like',"%$ref%");return view('admin.transactions',['transactions'=>$q->latest()->paginate(20)->withQueryString()]);}
}