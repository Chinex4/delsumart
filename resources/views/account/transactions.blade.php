@extends('layouts.student')
@section('title', $title)
@section('content')
    <x-page-header :title="$title" description="Follow every step, from payment to a successful handover."
        eyebrow="YOUR MARKETPLACE ACTIVITY" />
    <form class="filter-bar" method="GET">
        <x-field name="status" label="Transaction state" type="select">
            <option value="">All states</option>
            @foreach (config('ui.transaction_states') as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </x-field>
        <x-button variant="secondary" icon="filter">Filter</x-button>
        <a class="text-link mb-3" href="{{ url()->current() }}">Reset</a>
    </form>
    <div class="stack">
        @forelse($transactions as $tx)
            <x-card>
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="eyebrow">{{ $tx->buyer_id === auth()->id() ? 'PURCHASE' : 'SALE' }} ·
                            {{ $tx->created_at->format('d M Y') }}</p>
                        <h2 class="text-lg">
                            <a href="{{ route('listings.show', $tx->listing) }}">{{ $tx->listing->title }}</a>
                        </h2>
                        <p class="field-hint break-all">{{ $tx->paystack_reference }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold mb-2">₦{{ number_format($tx->amount, 2) }}</p>
                        <x-badge :status="$tx->status" />
                    </div>
                </div>
                <dl class="detail-list mt-6">
                    <div>
                        <dt>Buyer</dt>
                        <dd>{{ $tx->buyer->name }}</dd>
                    </div>
                    <div>
                        <dt>Seller</dt>
                        <dd>{{ $tx->seller->name }}</dd>
                    </div>
                    @if ($tx->paid_at)
                        <div>
                            <dt>Payment verified</dt>
                            <dd>{{ $tx->paid_at->format('d M Y, H:i') }}</dd>
                        </div>
                        @endif @if ($tx->completed_at)
                            <div>
                                <dt>Completed</dt>
                                <dd>{{ $tx->completed_at->format('d M Y, H:i') }}</dd>
                            </div>
                        @endif
                </dl>
                @if ($tx->status === 'disputed')
                    <x-alert tone="warning" class="mt-5 mb-0">This transaction is under dispute. Normal completion is paused
                        pending review. <a class="text-link" href="{{ route('account.disputes') }}">View disputes
                            →</a>
                    </x-alert>
                @endif
                <div class="flex flex-wrap gap-3 mt-5">
                    <x-transaction-actions :transaction="$tx" />
                    @if ($tx->status === 'pending_payment' && $tx->buyer_id === auth()->id() && $tx->listing->status === 'active')
                        <x-button :href="route('listings.show', $tx->listing)" variant="secondary">Return to item</x-button>
                    @endif
                </div>
            </x-card>
        @empty
            <x-card>
                <x-empty :title="'No ' . strtolower($title) . ' yet'" description="Your marketplace activity will appear here when you start trading.">
                    <x-button :href="route('listings.index')">Explore marketplace</x-button>
                </x-empty>
            </x-card>
        @endforelse
    </div>
    <div class="pagination">{{ $transactions->links() }}</div>
@endsection
