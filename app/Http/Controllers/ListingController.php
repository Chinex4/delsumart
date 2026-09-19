<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\FraudScoringService;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $r)
    {
        $q = Listing::with(['seller.verification', 'images'])->where('status', 'active');
        if ($s = $r->string('q')->trim()->value()) {
            $q->where(fn ($x) => $x->where('title', 'like', "%$s%")->orWhere('description', 'like', "%$s%")->orWhere('category', 'like', "%$s%"));
        }
        if ($c = $r->string('category')->value()) {
            $q->where('category', $c);
        }
        if ($min = $r->integer('min_price')) {
            $q->where('price', '>=', $min);
        }
        if ($max = $r->integer('max_price')) {
            $q->where('price', '<=', $max);
        }

        match ($r->string('sort')->value()) {
            'price_asc' => $q->orderBy('price'),
            'price_desc' => $q->orderByDesc('price'),
            'oldest' => $q->oldest(),
            default => $q->latest(),
        };

        return view('marketplace.index', ['listings' => $q->paginate(12)->withQueryString()]);
    }

    public function show(Listing $listing)
    {
        $listing->load(['seller.verification', 'images']);

        return view('marketplace.show', compact('listing'));
    }

    public function create()
    {
        return view('account.listing-create');
    }

    public function store(Request $r, FraudScoringService $fraud)
    {
        $d = $r->validate(['title' => 'required|string|max:120', 'description' => 'required|string|max:3000', 'category' => 'required|string|max:80', 'price' => 'required|numeric|min:100|max:10000000', 'images' => 'nullable|array|max:5', 'images.*' => 'image|max:4096']);
        $listing = $r->user()->listings()->create($d);
        foreach ($r->file('images', []) as $i => $image) {
            $listing->images()->create(['path' => $image->store('listings/'.$listing->id, 'public'), 'sort_order' => $i]);
        }
        $fraud->evaluate($r->user());

        return redirect()->route('listings.show', $listing)->with('success', 'Listing published.');
    }

    public function destroy(Request $r, Listing $listing)
    {
        abort_unless($listing->user_id === $r->user()->id, 403);
        $listing->update(['status' => 'removed']);

        return redirect()->route('dashboard');
    }
}
