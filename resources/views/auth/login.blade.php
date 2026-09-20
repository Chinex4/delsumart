@extends('layouts.auth')
@section('title', 'Welcome back')
@section('content')
    <p class="eyebrow">GOOD TO SEE YOU AGAIN</p>
    <h1>Welcome back.</h1>
    <p class="muted text-sm">Your campus marketplace is right where you left it.</p>
    <form method="POST" action="{{ route('login.store') }}" class="form-stack">
        @csrf
        <x-field name="login" label="Email or matric number" autocomplete="username"
            placeholder="you@example.com or your matric number" required autofocus />
        <x-field name="password" label="Password" type="password" autocomplete="current-password" required />
        <a href="{{ route('password.request') }}" class="text-link justify-end">Forgot password?</a>
        <x-button>Continue securely <x-icon name="arrow" size="17" />
        </x-button>
        <p class="field-hint text-center">
            <x-icon name="lock" size="13" /> We'll send an email code to confirm it's
            you.
        </p>
    </form>
    <p class="auth-footnote">New to DelsuMart? <a href="{{ route('register') }}">Create your account</a>
    </p>
@endsection
