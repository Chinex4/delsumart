@props(['transaction'])
@if (auth()->user()->isVerifiedStudent())
    @if ($transaction->status === 'paid_held' && $transaction->buyer_id === auth()->id())
        <form method="POST" action="{{ route('transactions.confirm', $transaction) }}"
            data-confirm="confirm-{{ $transaction->id }}">
            @csrf
            <x-button>Confirm receipt</x-button>
        </form>
        <x-modal :id="'confirm-' . $transaction->id" title="Everything received as expected?">
            <p class="muted text-sm">Confirm only after inspecting your item. This completes the transaction and ends the
                normal dispute window.</p>
            <div class="modal-actions">
                <x-button type="button" variant="secondary" data-close-dialog>Not
                    yet</x-button>
                <x-button type="button" data-confirm-action>Yes, confirm receipt</x-button>
            </div>
        </x-modal>
    @endif
    @if (in_array($transaction->status, ['paid_held', 'release_pending']) &&
            !$transaction->disputes->whereIn('status', ['open', 'under_review'])->count())
        <x-button type="button" variant="secondary" :data-dialog="'dispute-' . $transaction->id" icon="message">Raise a dispute</x-button>
        <x-modal :id="'dispute-' . $transaction->id" title="Tell us what went wrong">
            <form method="POST" action="{{ route('disputes.store', $transaction) }}" enctype="multipart/form-data"
                class="form-stack">
                @csrf
                <div class="field">
                    <label for="category-{{ $transaction->id }}">Issue
                        category</label>
                    <input class="input" id="category-{{ $transaction->id }}" name="category" maxlength="80"
                        value="{{ old('category') }}" placeholder="e.g. Item condition" required>
                </div>
                <div class="field">
                    <label for="details-{{ $transaction->id }}">What happened?</label>
                    <textarea class="input" id="details-{{ $transaction->id }}" name="details" maxlength="3000" rows="5" required>{{ old('details') }}</textarea>
                </div>
                <div class="field">
                    <label for="evidence-{{ $transaction->id }}">Evidence (optional)</label>
                    <input class="input" id="evidence-{{ $transaction->id }}" name="evidence" type="file"
                        accept=".jpg,.jpeg,.png,.pdf">
                    <p class="field-hint">JPG, PNG or PDF, up to 4 MB. Only the participants and administrators can
                        access it.</p>
                </div>
                <x-alert>Normal completion is paused while your dispute is reviewed.</x-alert>
                <x-button>Submit
                    dispute</x-button>
            </form>
        </x-modal>
    @endif
@endif
