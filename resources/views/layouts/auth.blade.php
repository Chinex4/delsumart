<!doctype html>
<html lang="en">

<head>@include('partials.head')</head>

<body>
    <a class="skip-link" href="#main-content">Skip to sign in form</a>
    <div class="auth-layout">
        <aside class="auth-story">
            <x-brand />
            <div>
                <p class="eyebrow">YOUR CAMPUS. YOUR COMMUNITY.</p>
                <h2>Good finds.<br>Great connections.<br>All on campus.</h2>
                <p>From your next laptop to your favourite textbook. Buy and sell with students you can trust.</p>
                <img src="{{ asset(config('marketplace.hero_image')) }}"
                    alt="Laptop and everyday study essentials on a desk">
                <div class="auth-story-footer">
                    <x-icon name="shield" size="16" /> Student verification · Protected
                    payment flow
                </div>
            </div>
            <small class="muted">Delta State University · Abraka</small>
        </aside>
        <main id="main-content" class="auth-main">
            <div class="auth-form">
                <x-brand />
                <a class="auth-back" href="{{ route('home') }}">← Back to
                    DelsuMart</a>
                <x-flash />@yield('content')
            </div>
        </main>
    </div>
</body>

</html>
