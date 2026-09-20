@extends('layouts.student')
@section('title', 'Your dashboard')
@section('content')
    <x-page-header :title="'Welcome back, ' . Str::before(auth()->user()->name, ' ') . '.'" description="Here's what's happening in your corner of the marketplace."
        eyebrow="YOUR CAMPUS, CONNECTED">
        <x-button :href="route('listings.create')" icon="plus">Create listing</x-button>
    </x-page-header>
    @if (!auth()->user()->isVerifiedStudent())
        <x-alert tone="warning" title="Your next step: get trade-ready">Complete your student verification to buy and sell. <a
                class="text-link" href="{{ route('kyc.show') }}">View verification →</a>
        </x-alert>
    @endif
    <div class="stats-grid">
        @foreach (config('ui.student_stats') as $key => $item)
            <x-stat :label="$item[0]" :value="$stats[$key]" :icon="$item[1]" />
        @endforeach
    </div>
    <div class="detail-grid mb-7">
        <x-card title="Recent purchases" description="Keep an eye on your latest campus finds.">
            @forelse($recent as $tx)
                <div class="flex justify-between gap-4 py-4 border-b border-slate-100 last:border-0">
                    <div>
                        <a href="{{ route('listings.show', $tx->listing) }}"
                            class="font-semibold text-sm">{{ $tx->listing->title }}</a>
                        <p class="field-hint">{{ $tx->created_at->format('d M Y') }} · {{ $tx->seller->name }}</p>
                    </div>
                    <div class="text-right">
                        <strong class="block text-sm mb-1">₦{{ number_format($tx->amount, 2) }}</strong>
                        <x-badge :status="$tx->status" />
                    </div>
                </div>
            @empty
                <x-empty title="Your first find is waiting" description="Explore items from the DELSU community.">
                    <x-button :href="route('listings.index')" variant="secondary">Browse marketplace</x-button>
                </x-empty>
            @endforelse
            @if ($recent->isNotEmpty())
                <a href="{{ route('account.purchases') }}" class="text-link mt-5">All purchases <x-icon name="arrow"
                        size="16" />
                </a>
            @endif
        </x-card>
        <div class="stack">
            <x-card title="Your account at a glance">
                <dl class="detail-list">
                    <div>
                        <dt>Student verification</dt>
                        <dd>
                            <x-badge :status="auth()->user()->verification?->verification_status ?? 'not_submitted'" />
                        </dd>
                    </div>
                    <div>
                        <dt>Account status</dt>
                        <dd>
                            <x-badge :status="auth()->user()->account_status" />
                        </dd>
                    </div>
                    <div>
                        <dt>Open disputes</dt>
                        <dd>
                            <a href="{{ route('account.disputes') }}" class="text-link">{{ $stats['disputes'] }} involving
                                your transactions</a>
                        </dd>
                    </div>
                    <div>
                        <dt>Sold listings</dt>
                        <dd>{{ $stats['sold_listings'] }}</dd>
                    </div>
                </dl>
            </x-card>
            <x-card title="Make your next move">
                <div class="form-stack">
                    <x-button :href="route('listings.create')" variant="secondary" icon="plus">Sell something on
                        campus</x-button>
                    <x-button :href="route('account.sales')" variant="secondary" icon="wallet">Manage your
                        sales</x-button>
                </div>
            </x-card>
        </div>
    </div>
    <div class="section-heading">
        <div>
            <h2>Your recent listings</h2>
            <p>A new home for the things you no longer need.</p>
        </div>
        <a class="text-link" href="{{ route('account.listings') }}">View all <x-icon name="arrow" size="16" />
        </a>
    </div>
    <div class="listing-grid">
        @forelse($recentListings as $listing)
            <x-listing-card :listing="$listing" />
        @empty
            <x-empty title="Make room for something new"
                description="Create your first listing when your student account is verified.">
                <x-button :href="route('listings.create')">Create listing</x-button>
            </x-empty>
        @endforelse
    </div>
@endsection
