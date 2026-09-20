@extends('layouts.app')
@section('title', $listing->title)
@section('content')
    <div class="container section">
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <x-icon name="chevron" size="12" />
            <a href="{{ route('listings.index') }}">Marketplace</a>
            <x-icon name="chevron" size="12" />
            <span>{{ $listing->category }}</span>
        </nav>
        <div class="product-grid">
            <div data-gallery>
                <div class="gallery-main">
                    @if ($listing->images->isNotEmpty())
                        <img data-main-image
                            src="{{ asset('storage/' . $listing->images->sortBy('sort_order')->first()->path) }}"
                            alt="{{ $listing->title }}">
                    @else
                        <div class="product-placeholder">
                            <x-icon :name="config('marketplace.categories')[$listing->category] ?? 'bag'" size="100" />
                            <span>The seller hasn't added photos yet.</span>
                        </div>
                    @endif
                </div>
                @if ($listing->images->count() > 1)
                    <div class="gallery-thumbs" aria-label="Product images">
                        @foreach ($listing->images->sortBy('sort_order') as $image)
                            <button type="button" data-gallery-image="{{ asset('storage/' . $image->path) }}"
                                aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                aria-label="Show product photo {{ $loop->iteration }}">
                                <img src="{{ asset('storage/' . $image->path) }}"
                                    alt="{{ $listing->title }}, photo {{ $loop->iteration }}">
                            </button>
                        @endforeach
                    </div>
                @endif
                <x-card title="About this item" class="mt-7">
                    <p class="prose-copy">{{ $listing->description }}</p>
                </x-card>
            </div>
            <div class="product-info">
                <span class="eyebrow">{{ $listing->category }}</span>
                <h1>{{ $listing->title }}</h1>
                <p class="muted text-xs mt-3">
                    <x-icon name="location" size="14" /> DELSU, Abraka · Listed
                    {{ $listing->created_at->format('d M Y') }}
                </p>
                <p class="product-price">₦{{ number_format($listing->price, 2) }}</p>
                <x-badge :status="$listing->status" />
                <div class="seller-card">
                    <x-avatar :user="$listing->seller" />
                    <div>
                        <p class="text-xs muted">Listed by</p>
                        <strong>{{ $listing->seller->name }}</strong>
                        <p class="muted text-xs">{{ $listing->seller->programme }} · {{ $listing->seller->level }} level
                        </p>
                    </div>
                    @if ($listing->seller->isVerifiedStudent())
                        <span class="verified-label ml-auto">
                            <x-icon name="shield" size="15" /> Verified
                            student</span>
                    @endif
                </div>
                @auth
                    @if (auth()->id() === $listing->user_id)
                        <x-alert title="This is your listing">You can manage it from your seller workspace.</x-alert>
                        <x-button :href="route('account.listings')" variant="secondary">Manage my listings</x-button>
                        @if ($listing->status === 'active' && auth()->user()->isVerifiedStudent())
                            <form method="POST" action="{{ route('listings.destroy', $listing) }}" class="mt-3"
                                data-confirm="remove-listing">
                                @csrf @method('DELETE')
                                <x-button variant="danger">Remove
                                    listing</x-button>
                            </form>
                            <x-modal id="remove-listing" title="Remove this listing?">
                                <p class="muted">It will no longer appear in the active marketplace.</p>
                                <div class="modal-actions">
                                    <x-button type="button" variant="secondary" data-close-dialog>Keep
                                        listing</x-button>
                                    <x-button type="button" variant="danger" data-confirm-action>Remove
                                        listing</x-button>
                                </div>
                            </x-modal>
                        @endif
                    @elseif($listing->status !== 'active')
                        <x-alert>This item is no longer available to purchase.</x-alert>
                    @elseif(!auth()->user()->isVerifiedStudent())
                        <x-alert title="Verify before you trade">Complete student verification to purchase this
                            item.</x-alert>
                        <x-button :href="route('kyc.show')" class="w-full" icon="shield">Complete
                            verification</x-button>
                    @else
                        <form method="POST" action="{{ route('payments.initialize', $listing) }}">
                            @csrf
                            <x-button class="w-full" icon="lock">Pay securely with Paystack</x-button>
                        </form>
                    @endif
                @else
                    <x-button :href="route('login')" class="w-full">Sign in to purchase <x-icon name="arrow" size="16" />
                </x-button>@endauth
                <div class="panel mt-6">
                    <div class="panel-body">
                        <h3 class="text-sm mb-3">A little more peace of mind</h3>
                        <ul class="space-y-3 text-xs muted">
                            <li class="flex gap-2">
                                <x-icon name="shield" size="16" /> Payment verified before
                                transaction completion.
                            </li>
                            <li class="flex gap-2">
                                <x-icon name="check" size="16" /> Only confirm receipt when you've
                                checked your item.
                            </li>
                            <li class="flex gap-2">
                                <x-icon name="message" size="16" /> Something wrong? Raise a dispute
                                before completion.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
