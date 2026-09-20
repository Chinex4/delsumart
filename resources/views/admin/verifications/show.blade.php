@extends('layouts.admin')
@section('title', 'Review student verification')
@section('content')
    <a class="text-link mb-5" href="{{ route('admin.verifications') }}">
        ← Verification queue</a>
    <x-page-header :title="$verification->full_name" eyebrow="PRIVATE STUDENT REVIEW"
        description="Compare the student details with both submitted documents before recording your decision.">
        <x-badge :status="$verification->verification_status" />
    </x-page-header>
    <div class="detail-grid">
        <div class="stack">
            <x-card title="Student information">
                <dl class="detail-list">
                    @foreach (['Full name' => $verification->full_name, 'Matric number' => $verification->matric_no, 'Email' => $verification->user->email, 'Programme' => $verification->programme, 'Level' => $verification->level, 'First submitted' => $verification->created_at->format('d M Y, H:i'), 'Resubmissions' => $verification->resubmission_count] as $label => $value)
                        <div>
                            <dt>{{ $label }}</dt>
                            <dd>{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-card>
            <x-card title="Submitted documents"
                description="Private documents for identity review. Open an image to inspect it in detail.">
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-kyc-document :verification="$verification" type="id-card" label="Student ID card" />
                    <x-kyc-document :verification="$verification" type="fee-receipt" label="Current school-fee receipt" />
                </div>
            </x-card>
            <x-card title="Review history">
                @forelse($history as $log)
                    <article class="py-4 border-b border-slate-100 last:border-0">
                        <p class="font-semibold text-sm">{{ Str::headline($log->action_type) }}</p>
                        <p class="text-sm muted mt-2">{{ $log->notes }}</p>
                        <p class="field-hint">{{ $log->admin?->name ?? 'System' }} ·
                            {{ $log->created_at->format('d M Y, H:i') }}</p>
                    </article>
                @empty
                    <p class="muted text-sm">No previous review decisions.</p>
                @endforelse
            </x-card>
        </div>
        <aside class="stack">
            @if ($verification->rejection_reason)
                <x-alert tone="warning" title="Previous rejection reason">{{ $verification->rejection_reason }}</x-alert>
            @endif
            <x-card title="Record a decision"
                description="The student will be notified and your decision will be recorded in the audit log.">
                @if ($verification->verification_status === 'pending')
                    <form method="POST" action="{{ route('admin.verifications.decide', $verification) }}"
                        data-confirm="approve-verification">
                        @csrf @method('PATCH')
                        <input type="hidden" name="decision" value="verified">
                        <x-button class="w-full" icon="check">Approve verification</x-button>
                    </form>
                    <x-button type="button" variant="danger" class="w-full mt-3" data-dialog="reject-verification">Reject
                        submission</x-button>
                    <x-modal id="approve-verification" title="Approve this student?">
                        <p class="muted text-sm mb-5">Confirm that both documents are readable, current and match this
                            student's identity.</p>
                        <x-button type="button" data-confirm-action>Confirm approval</x-button>
                    </x-modal>
                    <x-modal id="reject-verification" title="Request corrected documents">
                        <form method="POST" action="{{ route('admin.verifications.decide', $verification) }}"
                            class="form-stack">
                            @csrf @method('PATCH')
                            <input type="hidden" name="decision" value="rejected">
                            <x-field name="reason" label="Rejection reason" type="textarea" required maxlength="1000"
                                hint="Explain exactly what the student needs to correct." />
                            <x-button variant="danger">Confirm rejection</x-button>
                        </form>
                    </x-modal>
                @else
                    <p class="text-sm muted">Reviewed by {{ $verification->reviewer?->name ?? 'Administrator' }} on
                        {{ $verification->verified_at?->format('d M Y, H:i') }}.</p>
                @endif
            </x-card>
            <x-alert title="Handle with care">These documents contain personal information. Use them only to verify
                the student's identity.</x-alert>
            <x-button :href="route('admin.students.show', $verification->user)" variant="secondary">View student
                account</x-button>
        </aside>
    </div>
@endsection
