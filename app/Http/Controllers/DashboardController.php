<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function overview(Request $request)
    {
        $user = $request->user();

        return view('dashboard', [
            'stats' => [
                'active_listings' => $user
                    ->listings()
                    ->where('status', 'active')
                    ->count(),
                'sold_listings' => $user
                    ->listings()
                    ->where('status', 'sold')
                    ->count(),
                'purchases' => $user->purchases()->count(),
                'sales' => $user->sales()->count(),
                'held' => $user->purchases()->where('status', 'paid_held')->count() +
                    $user->sales()->where('status', 'paid_held')->count(),
                'disputes' => $this->disputeQuery($user->id)
                    ->whereIn('status', ['open', 'under_review'])
                    ->count(),
            ],
            'recent' => $user
                ->purchases()
                ->with(['listing', 'seller'])
                ->latest()
                ->take(5)
                ->get(),
            'recentListings' => $user
                ->listings()
                ->with(['images', 'seller.verification'])
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }

    public function account(Request $request)
    {
        return view('account.profile', [
            'user' => $request->user()->load('verification'),
        ]);
    }

    public function listings(Request $request)
    {
        return view('account.listings', [
            'listings' => $request
                ->user()
                ->listings()
                ->with('images')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function purchases(Request $request)
    {
        return view('account.transactions', [
            'title' => 'Purchases',
            'eyebrow' => 'BUYING',
            'transactions' => $request
                ->user()
                ->purchases()
                ->with(['listing.images', 'seller', 'buyer', 'disputes'])
                ->when(
                    $request->filled('status'),
                    fn ($query) => $query->where(
                        'status',
                        $request->string('status')->value(),
                    ),
                )
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'mode' => 'purchases',
        ]);
    }

    public function sales(Request $request)
    {
        return view('account.transactions', [
            'title' => 'Sales',
            'eyebrow' => 'SELLING',
            'transactions' => $request
                ->user()
                ->sales()
                ->with(['listing.images', 'buyer', 'seller', 'disputes'])
                ->when(
                    $request->filled('status'),
                    fn ($query) => $query->where(
                        'status',
                        $request->string('status')->value(),
                    ),
                )
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'mode' => 'sales',
        ]);
    }

    public function transactions(Request $request)
    {
        $transactions = Transaction::with([
            'listing.images',
            'buyer',
            'seller',
            'disputes',
        ])
            ->where(
                fn ($query) => $query
                    ->where('buyer_id', $request->user()->id)
                    ->orWhere('seller_id', $request->user()->id),
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string('status')->value(),
                ),
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('account.transactions', [
            'title' => 'Transactions',
            'eyebrow' => 'ACTIVITY',
            'transactions' => $transactions,
            'mode' => 'all',
        ]);
    }

    public function disputes(Request $request)
    {
        return view('account.disputes', [
            'disputes' => $this->disputeQuery($request->user()->id)
                ->with([
                    'transaction.listing',
                    'transaction.buyer',
                    'transaction.seller',
                    'complainant',
                ])
                ->latest()
                ->paginate(15),
        ]);
    }

    private function disputeQuery(int $userId)
    {
        return Dispute::query()->whereHas(
            'transaction',
            fn ($query) => $query
                ->where('buyer_id', $userId)
                ->orWhere('seller_id', $userId),
        );
    }
}
