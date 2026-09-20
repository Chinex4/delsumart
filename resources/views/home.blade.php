@extends('layouts.app')
@section('title', 'Your campus. Your marketplace.')
@section('content')
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="hero-label">
                    <x-icon name="location" size="13" /> Exclusively for DELSU students</span>
                <h1>Your campus.<br>Your people.<br>
                    <span>Your marketplace.</span>
                </h1>
                <p>Find what you need. Sell what you don't. A safer way to buy and sell with verified students, right here
                    in Abraka.</p>
                <div class="hero-actions">
                    <x-button :href="route('listings.index')">Browse marketplace <x-icon name="arrow" size="17" />
                    </x-button>
                    <x-button :href="route('listings.create')" variant="secondary" icon="plus">Start
                        selling</x-button>
                </div>
                <div class="hero-trust">
                    <x-icon name="shield" size="17" /> Verified students. Real connections. Safer
                    trading.
                </div>
            </div>
            <div class="hero-art">
                <img class="hero-image" src="{{ asset(config('marketplace.hero_image')) }}"
                    alt="A laptop and everyday essentials ready for a productive day on campus" width="1200"
                    height="800" fetchpriority="high">
                <span class="hero-image-label">A new semester. A fresh start.</span>
                <div class="hero-float">
                    <span class="trust-icon">
                        <x-icon name="shield" />
                    </span>
                    <div>
                        <strong>Student to student. Built on trust.</strong>
                        <p>Verified identities. Protected payment flow.</p>
                    </div>
                </div>
                <span class="hero-caption">Campus essentials, closer to you.</span>
            </div>
        </div>
    </section>
    <div class="trust-strip">
        <div class="container">
            <div class="trust-item">
                <x-icon name="shield" /> Verified student community
            </div>
            <div class="trust-item">
                <x-icon name="lock" /> Protected payment flow
            </div>
            <div class="trust-item">
                <x-icon name="location" /> Right here on campus
            </div>
            <div class="trust-item">
                <x-icon name="message" /> Dispute support
            </div>
        </div>
    </div>
    <section class="section container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">FIND YOUR NEXT CAMPUS ESSENTIAL</p>
                <h2>What are you looking for?</h2>
            </div>
            <a class="text-link" href="{{ route('listings.index') }}">Explore all <x-icon name="arrow" size="16" />
            </a>
        </div>
        <div class="category-grid">
            @foreach (config('marketplace.categories') as $category => $icon)
                <a class="category-card" href="{{ route('listings.index', ['category' => $category]) }}">
                    <span class="category-icon">
                        <x-icon :name="$icon" size="26" />
                    </span>{{ $category }}</a>
            @endforeach
        </div>
    </section>
    <section class="section section-white">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">FRESH FINDS, JUST LISTED</p>
                    <h2>New around campus</h2>
                    <p>{{ number_format($listingCount) }} active {{ Str::plural('listing', $listingCount) }}. Your next
                        great find could be right here.</p>
                </div>
                <a class="text-link" href="{{ route('listings.index') }}">View all listings <x-icon name="arrow"
                        size="16" />
                </a>
            </div>
            <div class="listing-grid">
                @forelse($recent as $listing)
                    <x-listing-card :listing="$listing" />
                @empty
                    <x-empty title="Be the first great find"
                        description="The marketplace is ready for your campus essentials. Get verified and publish your first listing.">
                        <x-button :href="route('listings.create')" icon="plus">Create a listing</x-button>
                    </x-empty>
                @endforelse
            </div>
        </div>
    </section>
    <section id="how-it-works" class="section container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">SIMPLE FROM START TO FINISH</p>
                <h2>Campus trading, without the guesswork.</h2>
            </div>
        </div>
        <div class="steps">
            @foreach (config('ui.steps') as $step)
                <article>
                    <span class="step-number">0{{ $loop->iteration }}</span>
                    <h3>{{ $step[0] }}</h3>
                    <p>{{ $step[1] }}</p>
                </article>
            @endforeach
        </div>
    </section>
    <section id="security" class="container pb-12">
        <div class="security-section">
            <div>
                <p class="eyebrow">TRUST IS PART OF THE DEAL</p>
                <h2>A marketplace that<br>looks out for you.</h2>
                <p>From who you're trading with to how a transaction ends, every step is designed to make campus commerce
                    more accountable.</p>
                <a class="btn btn-light mt-6" href="{{ route('kyc.show') }}">Get verified <x-icon name="arrow"
                        size="16" />
                </a>
            </div>
            <div class="security-grid">
                @foreach (config('ui.security') as $item)
                    <div>
                        <x-icon :name="$item[0]" size="25" />
                        <h3>{{ $item[1] }}</h3>
                        <p>{{ $item[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section id="faq" class="section section-white">
        <div class="container faq-layout">
            <div>
                <p class="eyebrow">A LITTLE MORE CLARITY</p>
                <h2>Good questions.<br>Straight answers.</h2>
                <p class="muted mt-4 text-sm">Everything you need to get started with confidence.</p>
            </div>
            <div class="faq">
                @foreach (config('ui.faq') as $faq)
                    <details>
                        <summary>{{ $faq[0] }}</summary>
                        <p>{{ $faq[1] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
    <section class="section container">
        <div class="cta-banner">
            <div>
                <h2>Your next great find is closer than you think.</h2>
                <p>Join your campus community. Make room for something new.</p>
            </div>
            <x-button :href="auth()->check() ? route('listings.index') : route('register')">{{ auth()->check() ? 'Explore marketplace' : 'Join DelsuMart' }} <x-icon
                    name="arrow" size="17" />
            </x-button>
        </div>
    </section>
@endsection
