<!doctype html>
<html lang="en">

<head>@include('partials.head')</head>

<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div class="top-note">Built for our campus. <span>Trade with verified DELSU students.</span>
    </div>
    <header class="public-header" x-data="navigation">
        <div class="container public-nav">
            <x-brand />
            <nav class="nav-links" aria-label="Main navigation">
                <a href="{{ route('listings.index') }}"
                    @if (request()->routeIs('listings.*')) aria-current="page" @endif>Marketplace</a>
                <a href="{{ route('home') }}#how-it-works">How it works</a>
                <a href="{{ route('home') }}#security">Safety & security</a>
            </nav>
            <div class="nav-actions">
                @auth
                    <x-button :href="route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard')" icon="grid">My dashboard</x-button>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <x-button :href="route('register')">Create account <x-icon name="arrow" size="16" />
                    </x-button>
                @endauth
            </div>
            <button type="button" class="icon-button mobile-toggle" @click="toggle" :aria-expanded="open"
                aria-controls="mobile-navigation" aria-label="Toggle navigation">
                <x-icon name="menu" />
            </button>
        </div>
        <nav id="mobile-navigation" class="mobile-nav" x-show="open" x-cloak @keydown.escape="close"
            aria-label="Mobile navigation">
            <a href="{{ route('listings.index') }}">Marketplace</a>
            <a href="{{ route('home') }}#how-it-works">How it
                works</a>
            <a href="{{ route('home') }}#security">Safety & security</a>
            @auth<x-button :href="route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard')">My dashboard</x-button>
            @else
                <a href="{{ route('login') }}">Log
                    in</a>
            <x-button :href="route('register')">Create account</x-button>@endauth
        </nav>
    </header>
    <main id="main-content">
        @if (session('success') || session('warning') || $errors->any())
            <div class="container pt-6">
                <x-flash />
            </div>
        @endif
        @yield('content')
    </main>
    @include('partials.footer')
</body>

</html>
