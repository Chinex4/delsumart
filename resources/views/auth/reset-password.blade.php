@extends('layouts.auth')
@section('title', 'Choose a new password')
@section('content')
    <p class="eyebrow">A FRESH START</p>
    <h1>Choose a new password.</h1>
    <p class="muted text-sm">Make it unique to keep your account secure.</p>
    <form method="POST" action="{{ route('password.update') }}" class="form-stack">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" label="Email address" type="email" :value="$email" autocomplete="email" required />
        <x-field name="password" label="New password" type="password" autocomplete="new-password" minlength="8"
            hint="At least 8 characters with uppercase, lowercase and a number." required />
        <x-field name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password"
            minlength="8" required />
        <x-button>Reset password</x-button>
    </form>
@endsection
