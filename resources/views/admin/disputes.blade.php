@extends('layouts.admin')
@section('title', 'Disputes')
@section('content')
    <x-page-header title="Dispute resolution" eyebrow="TRUST & SAFETY"
        description="Review the claim and evidence before deciding what happens to the transaction." />
    <div class="stack">
        @forelse($disputes as $d)
            <x-card>
                <div class="detail-grid">
                    <div>
                        <x-dispute-summary :dispute="$d" />
                    </div>
                    @if (in_array($d->status, ['open', 'under_review']))
                        <form method="POST" action="{{ route('admin.disputes.resolve', $d) }}" class="form-stack"
                            data-confirm="resolve-{{ $d->id }}">
                            @csrf @method('PATCH')
                            <div class="field">
                                <label for="decision-{{ $d->id }}">Decision</label>
                                <select id="decision-{{ $d->id }}" name="decision" class="input">
                                    <option value="resolved">Resolve</option>
                                    <option value="rejected">Reject dispute</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="action-{{ $d->id }}">Transaction action</label>
                                <select id="action-{{ $d->id }}" name="transaction_action" class="input">
                                    <option value="hold">Return to protected hold</option>
                                    <option value="release">Release / complete</option>
                                    <option value="refund">Mark refunded</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="resolution-{{ $d->id }}">Resolution notes</label>
                                <textarea id="resolution-{{ $d->id }}" name="resolution" required maxlength="2000" rows="4" class="input">{{ old('resolution') }}</textarea>
                            </div>
                            <x-button>Record decision</x-button>
                        </form>
                        <x-modal :id="'resolve-' . $d->id" title="Record this dispute decision?">
                            <p class="muted text-sm mb-5">This updates the protected transaction state and notifies both
                                participants. Check your selected action before confirming.</p>
                            <x-button type="button" data-confirm-action>Confirm decision</x-button>
                        </x-modal>
                    @endif
                </div>
            </x-card>
        @empty
            <x-card>
                <x-empty title="No disputes to review"
                    description="Submitted disputes and their evidence will appear here." />
            </x-card>
        @endforelse
    </div>
    <div class="pagination">{{ $disputes->links() }}</div>
@endsection
