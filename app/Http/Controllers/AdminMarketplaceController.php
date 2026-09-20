<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminMarketplaceController extends Controller
{
    public function listings(Request $r)
    {
        $q = Listing::with(['seller', 'images']);
        if ($s = $r->string('q')->trim()->value()) {
            $q->where('title', 'like', "%$s%");
        }
        if ($status = $r->string('status')->value()) {
            $q->where('status', $status);
        }

        return view('admin.listings', [
            'listings' => $q->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function transactions(Request $r)
    {
        $q = Transaction::with(['listing', 'buyer', 'seller']);
        if ($status = $r->string('status')->value()) {
            $q->where('status', $status);
        }
        if ($ref = $r->string('reference')->trim()->value()) {
            $q->where('paystack_reference', 'like', "%$ref%");
        }

        foreach (['buyer', 'seller'] as $party) {
            if ($name = $r->string($party)->trim()->value()) {
                $q->whereHas(
                    $party,
                    fn ($user) => $user->where('name', 'like', "%$name%"),
                );
            }
        }
        $dates = $r->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);
        if ($dates['from'] ?? null) {
            $q->whereDate('created_at', '>=', $dates['from']);
        }
        if ($dates['to'] ?? null) {
            $q->whereDate('created_at', '<=', $dates['to']);
        }

        return view('admin.transactions', [
            'transactions' => $q->latest()->paginate(20)->withQueryString(),
        ]);
    }
}
