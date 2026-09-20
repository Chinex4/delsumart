@extends('layouts.auth')
@section('title', 'Verify your sign-in')
@section('content')
    <span class="empty-icon">
        <x-icon name="mail" size="28" />
    </span>
    <p class="eyebrow">ONE MORE STEP. A SAFER ACCOUNT.</p>
    <h1>Check your inbox.</h1>
    <p class="muted text-sm">Enter the six-digit code we sent to your email to finish signing in.</p>
    <form method="POST" action="{{ route('mfa.verify') }}" class="form-stack">
        @csrf
        <x-field name="code" label="Verification code" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6"
            autocomplete="one-time-code" placeholder="000000" class="text-center text-2xl tracking-[.5em]"
            hint="Your code is valid for 10 minutes. Check your spam folder too." required autofocus />
        <x-button icon="shield">Verify and sign in</x-button>
    </form>
    <p class="auth-footnote">Need a new code? <a href="{{ route('login') }}">Return to sign in</a>
    </p>
    <p class="field-hint text-center mt-2">Wait at least one minute before requesting another code.</p>
@endsection
