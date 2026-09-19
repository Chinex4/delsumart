@extends('layouts.admin')
@section('title', 'Review student verification')
@section('content')
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.verifications') }}">KYC reviews</a><x-icon name="chevron"
            size="12" /><span>{{ $verification->full_name }}</span>
    </nav>
    <x-page-header :title="$verification->full_name" description="Review both documents and student details before recording a decision."
        eyebrow="PRIVATE STUDENT VERIFICATION"><x-badge :status="$verification->verification_status" /></x-page-header>
    <div class="detail-grid">
        <div class="stack"><x-card title="Student information">
                <dl class="detail-list">
                    @foreach (['Full name' => $verification->full_name, 'Matric number' => $verification->matric_no, 'Email' => $verification->user->email, 'Programme' => $verification->programme, 'Level' => $verification->level, 'First submitted' => $verification->created_at->format('d M Y, H:i'), 'Last submission' => $verification->updated_at->format('d M Y, H:i'), 'Resubmissions' => $verification->resubmission_count] as $label => $value)
                        <div>
                            <dt>{{ $label }}</dt>
                            <dd>{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
                <a class="text-link mt-6" href="{{ route('admin.students.show', $verification->user) }}">View student
                    profile <x-icon name="arrow" size="15" /></a>
            </x-card>
            <x-card title="Submitted identity documents"
                description="Open an image for a larger preview, or view the original PDF securely.">
                <div class="form-grid"><x-kyc-document :verification="$verification" type="id-card"
                        label="Student ID card" /><x-kyc-document :verification="$verification" type="fee-receipt"
                        label="Current school-fee receipt" /></div>
            </x-card>
            <x-card title="Review history">
                @forelse($history as $log)
                    <div class="py-4 border-b border-slate-100 last:border-0">
                        <div class="flex justify-between gap-3"><strong
                                class="text-xs">{{ ucfirst(str_replace('_', ' ', $log->action_type)) }}</strong><time
                                class="field-hint">{{ $log->created_at->format('d M Y, H:i') }}</time></div>
                        <p class="prose-copy mt-2">{{ $log->notes }}</p>
                        <p class="field-hint">{{ $log->admin?->name ?? 'System' }}</p>
                </div>@empty<p class="muted text-sm">No previous review decisions have been recorded.</p>
                @endforelse
            </x-card>
        </div>
        <aside class="stack content-start">
            @if ($verification->rejection_reason)
                <x-alert tone="danger" title="Previous rejection reason">{{ $verification->rejection_reason }}</x-alert>
            @endif
            <x-card title="Record your decision" description="All decisions are logged and the student is notified.">
                @if ($verification->verification_status === 'pending')
                    <div class="form-stack">
                        <p class="muted text-xs">Check that the name and matric number match, both documents are readable,
                            and the fee receipt is for the current session.</p>
                        <form method="POST" action="{{ route('admin.verifications.decide', $verification) }}"
                            data-confirm="approve-kyc">@csrf @method('PATCH')<input type="hidden" name="decision"
                                value="verified"><x-button icon="check" class="w-full">Approve verification</x-button>
                        </form><x-button variant="secondary" type="button" data-dialog="reject-kyc" class="w-full">Reject
                            with a reason</x-button>
                    </div>
                    <x-modal id="approve-kyc" title="Approve this student's identity?">
                        <p class="muted text-sm">Confirm you have reviewed both documents for
                            {{ $verification->full_name }}. This approves student verification and unlocks trading for an
                            active account.</p>
                        <div class="modal-actions"><x-button type="button" variant="secondary" data-close-dialog>Back to
                                review</x-button><x-button type="button" data-confirm-action>Approve student</x-button>
                        </div>
                    </x-modal>
                    <x-modal id="reject-kyc" title="Explain what needs to change">
                        <form method="POST" action="{{ route('admin.verifications.decide', $verification) }}"
                            class="form-stack">@csrf @method('PATCH')<input type="hidden" name="decision"
                                value="rejected">
                            <p class="muted text-sm">The student will receive this reason so they can correct their
                                documents and resubmit.</p><x-field name="reason" label="Rejection reason" type="textarea"
                                maxlength="1000" required placeholder="Explain which document needs attention and why." />
                            <div class="modal-actions"><x-button type="button" variant="secondary"
                                    data-close-dialog>Cancel</x-button><x-button variant="danger">Confirm
                                    rejection</x-button></div>
                        </form>
                    </x-modal>
                @else<p class="muted text-sm">This submission has already been reviewed.</p>
                    <p class="text-xs mt-4">
                        {{ $verification->reviewer?->name }}<br>{{ $verification->verified_at?->format('d M Y, H:i') }}
                    </p>
                @endif
            </x-card><x-alert title="Private by design">Files stay on private storage. Document access is checked on every
                request.</x-alert>
        </aside>
    </div>
@endsection
