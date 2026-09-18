<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $r)
    {
        $u = $r->user();

        return view('dashboard', ['stats' => ['active_listings' => $u->listings()->where('status', 'active')->count(), 'sold_listings' => $u->listings()->where('status', 'sold')->count(), 'purchases' => $u->purchases()->count(), 'sales' => $u->sales()->count(), 'held' => $u->purchases()->where('status', 'paid_held')->count() + $u->sales()->where('status', 'paid_held')->count(), 'disputes' => Dispute::where('complainant_id', $u->id)->whereIn('status', ['open', 'under_review'])->count()], 'recent' => $u->purchases()->with('listing')->latest()->take(5)->get()]);
    }
}
