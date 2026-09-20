@props(['dispute'])
<div class="flex flex-wrap justify-between gap-3 mb-5">
    <div>
        <p class="eyebrow">DISPUTE #{{ $dispute->id }}</p>
        <h2 class="text-lg">{{ $dispute->transaction->listing->title }}</h2>
    </div>
    <x-badge :status="$dispute->status" />
</div>
<dl class="detail-list">
    <div>
        <dt>Buyer</dt>
        <dd>{{ $dispute->transaction->buyer->name }}</dd>
    </div>
    <div>
        <dt>Seller</dt>
        <dd>{{ $dispute->transaction->seller->name }}</dd>
    </div>
    <div>
        <dt>Amount</dt>
        <dd>₦{{ number_format($dispute->transaction->amount, 2) }}</dd>
    </div>
    <div>
        <dt>Reference</dt>
        <dd class="text-xs">{{ $dispute->transaction->paystack_reference }}</dd>
    </div>
</dl>
<div class="bg-slate-50 rounded-lg p-4 my-5">
    <h3 class="text-sm">{{ $dispute->category }}</h3>
    <p class="prose-copy mt-2">{{ $dispute->details }}</p>
</div>
@if ($dispute->evidence_path)
    <a class="text-link mb-5" href="{{ route('disputes.evidence', $dispute) }}" target="_blank" rel="noopener">
        <x-icon name="file" size="16" /> View private evidence</a>
@endif
<ol class="timeline">
    <li>
        <strong>Dispute opened by
            {{ $dispute->complainant->name }}</strong>
        <small>{{ $dispute->created_at->format('d M Y, H:i') }}</small>
    </li>
    @if ($dispute->resolved_at)
        <li>
            <strong>Review completed</strong>
            <small>{{ $dispute->resolved_at->format('d M Y, H:i') }}</small>
            <p class="prose-copy mt-2">{{ $dispute->resolution }}</p>
        </li>
    @else
        <li>
            <strong>Awaiting administrative review</strong>
            <small>Normal transaction completion is
                paused.</small>
        </li>
    @endif
</ol>
