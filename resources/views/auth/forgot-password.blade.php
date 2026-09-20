@extends('layouts.auth')
@section('title', 'Reset your password')
@section('content')
    <p class="eyebrow">LET'S GET YOU BACK IN</p>
    <h1>Forgot your password?</h1>
    <p class="muted text-sm">Enter your account email. We'll send you a password reset link.</p>
    <form method="POST" action="{{ route('password.email') }}" class="form-stack">
        @csrf
        <x-field name="email" label="Email address" type="email" autocomplete="email" required autofocus />
        <x-button>Send reset link <x-icon name="arrow" size="17" />
        </x-button>
    </form>
    <p class="auth-footnote">
        <a href="{{ route('login') }}">Back to sign in</a>
    </p>
@endsection
