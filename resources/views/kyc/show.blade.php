@extends('layouts.student')
@section('title', 'Student verification')
@section('content')
    <x-page-header title="A little verification. A lot more trust."
        description="Confirm your DELSU identity to unlock buying and selling." eyebrow="STUDENT VERIFICATION">
        <x-badge :status="$verification?->verification_status ?? 'not_submitted'" />
    </x-page-header>
    <div class="detail-grid">
        <div class="stack">
            @if ($verification?->verification_status === 'verified')
                <x-alert tone="success"
                    title="You're a verified DELSU student">{{ auth()->user()->account_status === 'active' ? 'Your trading access is unlocked. You’re ready to explore.' : 'Your identity is verified, but your account is suspended. Trading is unavailable.' }}</x-alert>
            @elseif($verification?->verification_status === 'rejected')
                <x-alert tone="danger"
                    title="Your submission needs attention">{{ $verification->rejection_reason }}</x-alert>
            @elseif($verification)
                <x-alert tone="warning" title="Your documents are under review">Trading stays locked until an administrator
                    approves your documents. You can replace your submission if needed.</x-alert>
            @else
                <x-alert title="Let's make your account trade-ready">A student ID and current fee receipt help us keep
                    this community for real DELSU students.</x-alert>
            @endif
            <x-card title="Your student details">
                <dl class="detail-list">
                    @foreach (['Full name' => auth()->user()->name, 'Matric number' => auth()->user()->matric_no, 'Programme' => auth()->user()->programme, 'Level' => auth()->user()->level] as $label => $value)
                        <div>
                            <dt>{{ $label }}</dt>
                            <dd>{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-card>
            @if ($verification)
                <x-card title="Your submitted documents" :description="'Last updated ' . $verification->updated_at->format('d M Y, H:i')">
                    <div class="form-grid">
                        <x-kyc-document :verification="$verification" type="id-card" label="Student ID card" />
                        <x-kyc-document :verification="$verification" type="fee-receipt" label="School-fee receipt" />
                    </div>
                </x-card>
            @endif
            @if ($verification?->verification_status !== 'verified')
                <x-card :title="$verification ? 'Replace your documents' : 'Upload your documents'" description="Clear, complete copies help administrators review your identity.">
                    <form method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data" class="form-stack">
                        @csrf
                        <x-field name="id_card" label="Student ID card" type="file" accept=".jpg,.jpeg,.png,.pdf"
                            hint="JPG, PNG or PDF. Maximum 4 MB. Keep your name and matric number visible." required />
                        <x-field name="fee_receipt" label="Current school-fee receipt / breakdown" type="file"
                            accept=".jpg,.jpeg,.png,.pdf"
                            hint="JPG, PNG or PDF. Maximum 4 MB. Upload the current academic session." required />
                        <x-button
                            icon="upload">{{ $verification ? 'Resubmit for review' : 'Submit for verification' }}</x-button>
                    </form>
                </x-card>
            @endif
        </div>
        <aside class="stack content-start">
            <x-card title="Your verification journey">
                <ol class="timeline">
                    <li>
                        <strong>01 · Create your account</strong>
                        <small>Completed</small>
                    </li>
                    <li>
                        <strong>02 · Submit your
                            documents</strong>
                        <small>{{ $verification ? 'Submitted ' . $verification->updated_at->format('d M Y') : 'Your next step' }}</small>
                    </li>
                    <li>
                        <strong>03 · Administrator
                            review</strong>
                        <small>{{ $verification?->verification_status === 'verified' ? 'Approved' : 'Documents are checked privately' }}</small>
                    </li>
                    <li>
                        <strong>04 · Start trading</strong>
                        <small>Buy and sell with your campus community</small>
                    </li>
                </ol>
            </x-card>
            <x-card title="Your privacy matters">
                <x-icon name="lock" class="text-cobalt mb-3" size="26" />
                <p class="muted text-xs">Your documents aren't public. Only you and authorized administrators can view them.
                    Other students never see your KYC files.</p>
            </x-card>
        </aside>
    </div>
@endsection
