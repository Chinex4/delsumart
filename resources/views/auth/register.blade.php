@extends('layouts.auth')
@section('title', 'Join your campus community')
@section('content')
    <p class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</p>
    <h1>Join your campus community.</h1>
    <p class="muted text-sm">One account. A whole campus of possibilities.</p>
    <form method="POST" action="{{ route('register.store') }}" class="form-stack">
        @csrf
        <x-field name="name" label="Full name" autocomplete="name" maxlength="100" required />
        <div class="form-grid">
            <x-field name="matric_no" label="Matric number" maxlength="30" placeholder="DELSU/CSC/001" required />
            <x-field name="level" label="Level" maxlength="20" placeholder="e.g. 300" required />
        </div>
        <x-field name="email" label="Email address" type="email" autocomplete="email" maxlength="255" required />
        <x-field name="programme" label="Programme of study" maxlength="100" placeholder="e.g. Computer Science" required />
        <div class="form-grid">
            <x-field name="password" label="Password" type="password" autocomplete="new-password" minlength="8"
                hint="At least 8 characters, uppercase, lowercase and a number." required />
            <x-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password"
                minlength="8" required />
        </div>
        <x-button>Create my account <x-icon name="arrow" size="17" />
        </x-button>
        <p class="field-hint text-center">Next, verify your student identity to start buying and selling.</p>
    </form>
    <p class="auth-footnote">Already part of the community? <a href="{{ route('login') }}">Log in</a>
    </p>
@endsection
