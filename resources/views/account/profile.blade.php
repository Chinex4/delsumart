@extends('layouts.student')
@section('title', 'Your account')
@section('content')
    <x-page-header title="Your place in the community." description="Your student profile and account security, at a glance."
        eyebrow="MY ACCOUNT" />
    <div class="detail-grid">
        <x-card title="Student profile">
            <div class="flex items-center gap-4 mb-7">
                <x-avatar :user="$user" class="w-14 h-14 text-xl" />
                <div>
                    <h2>{{ $user->name }}</h2>
                    <p class="muted text-xs">Member since {{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>
            <dl class="detail-list">
                @foreach (['Email' => $user->email, 'Matric number' => $user->matric_no, 'Programme' => $user->programme, 'Level' => $user->level] as $label => $value)
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd>{{ $value }}</dd>
                    </div>
                @endforeach
                <div>
                    <dt>Account status</dt>
                    <dd>
                        <x-badge :status="$user->account_status" />
                    </dd>
                </div>
                <div>
                    <dt>Student verification</dt>
                    <dd>
                        <x-badge :status="$user->verification?->verification_status ?? 'not_submitted'" />
                    </dd>
                </div>
            </dl>
        </x-card>
        <x-card title="Security comes with your account">
            <div class="stack">
                <div>
                    <x-icon name="lock" class="text-cobalt mb-3" />
                    <h3 class="text-sm">Email verification at sign-in</h3>
                    <p class="muted text-xs mt-2">Your password and a one-time email code work together to protect your
                        account.</p>
                </div>
                <x-button :href="route('kyc.show')" variant="secondary" icon="shield">Manage verification</x-button>
                <p class="field-hint">To reset your password, log out and choose “Forgot password?” on the sign-in page.</p>
            </div>
        </x-card>
    </div>
@endsection
