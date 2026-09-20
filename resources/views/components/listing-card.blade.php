@props(['listing'])
<article class="listing-card">
    <a href="{{ route('listings.show', $listing) }}" class="listing-photo" aria-label="View {{ $listing->title }}">
        @if ($listing->images->first())
            <img src="{{ asset('storage/' . $listing->images->sortBy('sort_order')->first()->path) }}"
                alt="{{ $listing->title }}" loading="lazy" width="600" height="450">
        @else
            <div class="product-placeholder">
                <x-icon :name="config('marketplace.categories')[$listing->category] ?? 'bag'" size="64" />
                <span>No photo uploaded</span>
            </div>
        @endif
        <span class="photo-label">{{ $listing->category }}</span>
    </a>
    <div class="listing-body">
        <div class="listing-meta">
            <span>
                <x-icon name="location" size="13" /> DELSU,
                Abraka</span>
            <span>{{ $listing->created_at->diffForHumans(null, true) }}</span>
        </div>
        <h3>
            <a href="{{ route('listings.show', $listing) }}">{{ $listing->title }}</a>
        </h3>
        <p class="listing-price">₦{{ number_format($listing->price, 2) }}</p>
        <div class="listing-seller">
            <span>
                <x-avatar :user="$listing->seller" />{{ $listing->seller->name }}</span>
            @if ($listing->seller->isVerifiedStudent())
                <span class="verified-label">
                    <x-icon name="shield" size="14" /> Verified</span>
            @endif
        </div>
    </div>
</article>
