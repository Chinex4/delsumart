@extends('layouts.admin')
@section('title', 'Student payouts')
@section('content')
    <x-page-header title="Student payouts" eyebrow="FINANCE OPERATIONS"
        description="Review withdrawal requests and record each payout decision." />
    <div class="stats-grid">
        <x-stat label="Pending requests" :value="$stats['pending']" icon="clock" />
        <x-stat label="Processing" :value="$stats['processing']" icon="wallet" />
        <x-stat label="Paid out" :value="'₦' . number_format($stats['paid_amount'], 2)" icon="check" />
    </div>
    <form method="GET" class="filter-bar">
        <x-field name="status" label="Payout status" type="select">
            <option value="">All statuses</option>
            @foreach (['pending', 'processing', 'paid', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </x-field>
        <x-button variant="secondary">Filter</x-button>
        <a class="text-link mb-3" href="{{ route('admin.payouts') }}">Reset</a>
    </form>
    <div class="stack">
        @forelse ($payouts as $payout)
            <x-card>
                <div class="detail-grid">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-lg">{{ $payout->user->name }}</h2>
                            <x-badge :status="$payout->status" />
                        </div>
                        <p class="field-hint">{{ $payout->user->matric_no }} · {{ $payout->user->email }}</p>
                        <dl class="detail-list mt-5">
                            <div>
                                <dt>Amount</dt>
                                <dd>₦{{ number_format($payout->amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt>Bank</dt>
                                <dd>{{ $payout->bankAccount->bank_name }}</dd>
                            </div>
                            <div>
                                <dt>Account name</dt>
                                <dd>{{ $payout->bankAccount->account_name }}</dd>
                            </div>
                            <div>
                                <dt>Account number</dt>
                                <dd>{{ $payout->bankAccount->account_number }}</dd>
                            </div>
                            <div>
                                <dt>Reference</dt>
                                <dd class="break-all">{{ $payout->reference }}</dd>
                            </div>
                            <div>
                                <dt>Requested</dt>
                                <dd>{{ $payout->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                    @if (in_array($payout->status, ['pending', 'processing']))
                        <form method="POST" action="{{ route('admin.payouts.update', $payout) }}" class="form-stack"
                            data-confirm="payout-{{ $payout->id }}">
                            @csrf
                            @method('PATCH')
                            <div class="field">
                                <label for="status-{{ $payout->id }}">Update request</label>
                                <select name="status" id="status-{{ $payout->id }}" class="input">
                                    <option value="processing">Mark processing</option>
                                    <option value="paid">Mark paid</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="note-{{ $payout->id }}">Administrative note</label>
                                <textarea name="admin_note" id="note-{{ $payout->id }}" class="input" rows="3" maxlength="1000"
                                    aria-describedby="note-help-{{ $payout->id }}">{{ old('admin_note') }}</textarea>
                                <p id="note-help-{{ $payout->id }}" class="field-hint">A reason is required when
                                    rejecting a request.</p>
                            </div>
                            <x-button>Save & audit</x-button>
                        </form>
                        <x-modal :id="'payout-' . $payout->id" title="Record this payout decision?">
                            <p class="text-sm muted mb-5">Check the selected status and bank details. Mark a request as paid
                                only after payment has been completed.</p>
                            <x-button type="button" data-confirm-action>Confirm decision</x-button>
                        </x-modal>
                    @else
                        <div>
                            <h3 class="text-sm">Final decision</h3>
                            <p class="muted text-sm mt-3">{{ $payout->admin_note ?: 'No note supplied.' }}</p>
                        </div>
                    @endif
                </div>
            </x-card>
        @empty
            <x-card>
                <x-empty title="No matching requests" description="Payout requests matching your filter will appear here."
                    icon="wallet" />
            </x-card>
        @endforelse
    </div>
    <div class="pagination">{{ $payouts->links() }}</div>
@endsection
