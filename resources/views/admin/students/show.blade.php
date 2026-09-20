@extends('layouts.admin')
@section('title', 'Student account')
@section('content')
    <a class="text-link mb-5" href="{{ route('admin.students') }}">
        ← All students</a>
    <x-page-header :title="$student->name" :description="$student->matric_no . ' · ' . $student->programme . ' · Level ' . $student->level" eyebrow="STUDENT ACCOUNT">
        <x-badge :status="$student->account_status" />
    </x-page-header>
    <div class="detail-grid">
        <div class="stack">
            <x-card title="Profile & verification">
                <dl class="detail-list">
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $student->email }}</dd>
                    </div>
                    <div>
                        <dt>Joined</dt>
                        <dd>{{ $student->created_at->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt>Verification</dt>
                        <dd>
                            <x-badge :status="$student->verification?->verification_status ?? 'not_submitted'" />
                        </dd>
                    </div>
                </dl>
                @if ($student->verification)
                    <x-button :href="route('admin.verifications.show', $student->verification)" variant="secondary" class="mt-5">Review identity documents</x-button>
                @endif
            </x-card>
            <x-card title="Recent listings">
                @forelse($student->listings as $listing)
                    <div class="flex flex-wrap justify-between gap-3 py-3 border-b border-slate-100 last:border-0">
                        <a class="text-link" href="{{ route('listings.show', $listing) }}">{{ $listing->title }}</a>
                        <span class="text-sm">₦{{ number_format($listing->price, 2) }} <x-badge :status="$listing->status" />
                        </span>
                    </div>
                @empty
                    <p class="muted text-sm">No listings yet.</p>
                @endforelse
            </x-card>
            @foreach (['purchases' => 'Recent purchases', 'sales' => 'Recent sales'] as $relation => $label)
                <x-card :title="$label">
                    @forelse($student->$relation as $tx)
                        <div class="flex flex-wrap justify-between gap-3 py-3 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="font-semibold text-sm">{{ $tx->listing->title }}</p>
                                <a class="field-hint text-link break-all"
                                    href="{{ route('admin.transactions', ['reference' => $tx->paystack_reference]) }}">{{ $tx->paystack_reference }}</a>
                            </div>
                            <div class="text-right">
                                <p class="text-sm mb-2">₦{{ number_format($tx->amount, 2) }}</p>
                                <x-badge :status="$tx->status" />
                            </div>
                        </div>
                    @empty
                        <p class="muted text-sm">No activity yet.</p>
                    @endforelse
                </x-card>
            @endforeach
            <x-card title="Recent disputes">
                @forelse($disputes as $dispute)
                    <div class="py-3 border-b border-slate-100 last:border-0">
                        <p class="font-semibold text-sm">{{ $dispute->transaction->listing->title }}</p>
                        <p class="field-hint">{{ $dispute->category }} · {{ $dispute->created_at->format('d M Y') }}</p>
                        <x-badge :status="$dispute->status" />
                    </div>
                @empty
                    <p class="muted text-sm">No disputes involving this student.</p>
                @endforelse
                <a class="text-link mt-4" href="{{ route('admin.disputes') }}">
                    Dispute workspace →</a>
            </x-card>
        </div>
        <aside class="stack">
            <x-card title="Risk indicators">
                <p class="text-3xl font-bold mb-3">{{ $risk['score'] }}<span class="text-base muted"> / 100</span>
                </p>
                <x-badge :status="$risk['level']" />
                <p class="field-hint mt-3">Signals for review, not proof of misconduct.</p>
                <ul class="mt-4 space-y-2 text-sm muted">
                    @forelse($risk['reasons'] as $reason)
                        <li>{{ $reason }}</li>
                    @empty
                        <li>No active risk rules triggered.</li>
                    @endforelse
                </ul>
            </x-card>
            <x-card title="Account controls">
                <form method="POST" action="{{ route('admin.students.status', $student) }}" class="form-stack"
                    data-confirm="account-decision">
                    @csrf @method('PATCH')
                    <x-field name="account_status" label="Account status" type="select">
                        @foreach (['active', 'suspended'] as $status)
                            <option value="{{ $status }}" @selected(old('account_status', $student->account_status) === $status)>{{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </x-field>
                    <x-field name="reason" label="Administrative reason" type="textarea" required maxlength="1000" />
                    <x-button>Apply & audit</x-button>
                </form>
            </x-card>
            <x-modal id="account-decision" title="Update this account?">
                <p class="muted text-sm mb-5">The selected status affects this student's ability to trade. Your reason will
                    be recorded.</p>
                <x-button type="button" data-confirm-action>Confirm update</x-button>
            </x-modal>
            <x-card title="Account audit history">
                @forelse($auditLogs as $log)
                    <div class="py-3 border-b border-slate-100 last:border-0">
                        <p class="font-semibold text-sm">{{ Str::headline($log->action_type) }}</p>
                        <p class="field-hint">{{ $log->notes }}</p>
                        <p class="field-hint">{{ $log->created_at->format('d M Y, H:i') }}</p>
                    </div>
                @empty
                    <p class="muted text-sm">No account decisions recorded.</p>
                @endforelse
            </x-card>
        </aside>
    </div>
@endsection
