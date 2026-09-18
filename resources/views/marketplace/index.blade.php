@extends('layouts.app') @section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-700">DELSU MARKETPLACE</p>
                <h1 class="mt-2 text-4xl font-black text-blue-950">Find what you need on campus.</h1>
            </div>
            <form class="flex w-full max-w-xl gap-2"><input name="q" value="{{ request('q') }}"
                    placeholder="Search phones, books, services…"
                    class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-4 py-3"><button
                    class="rounded-lg bg-blue-950 px-5 font-bold text-white">Search</button></form>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($listings as $listing)
                <a href="{{ route('listings.show', $listing) }}"
                    class="group overflow-hidden border border-slate-200 bg-white">
                    <div class="aspect-[4/3] bg-slate-100">
                        @if ($listing->images->first())
                            <img src="{{ asset('storage/' . $listing->images->first()->path) }}" alt=""
                            class="h-full w-full object-cover">@else<div
                                class="flex h-full items-center justify-center text-sm text-slate-400">No image</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-blue-700">{{ $listing->category }}</p>
                        <h2 class="mt-1 line-clamp-1 font-bold group-hover:text-blue-800">{{ $listing->title }}</h2>
                        <p class="mt-3 text-xl font-black text-blue-950">₦{{ number_format($listing->price, 2) }}</p>
                        <p class="mt-3 text-xs text-slate-500">{{ $listing->seller->name }} · @if ($listing->seller->isVerifiedStudent())
                                <span class="font-bold text-emerald-700">Verified</span>
                            @endif
                        </p>
                    </div>
            </a>@empty<div class="col-span-full border border-dashed bg-white p-12 text-center">
                    <h2 class="font-bold">No matching listings yet</h2>
                    <p class="mt-2 text-sm text-slate-500">Try a broader search or check back when students add more items.
                    </p>
                </div>
            @endforelse
        </div>
        <div class="mt-10">{{ $listings->links() }}</div>
    </div>
@endsection
