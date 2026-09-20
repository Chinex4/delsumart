@props(['status' => 'pending'])
@php
    $labels = [
        'paid_held' => 'Paid / Protected',
        'pending_payment' => 'Pending payment',
        'release_pending' => 'Release pending',
        'not_submitted' => 'Not submitted',
        'under_review' => 'Under review',
    ];
    $tone = match ($status) {
        'verified', 'active', 'released', 'resolved', 'paid' => 'success',
        'rejected', 'suspended', 'high', 'disputed' => 'danger',
        'pending', 'pending_payment', 'release_pending', 'open', 'medium', 'under_review' => 'warning',
        'paid_held', 'reviewed', 'processing' => 'blue',
        default => 'neutral',
    };
@endphp
<span {{ $attributes->class(['badge', 'badge-' . $tone]) }}>
    <span class="status-dot">
    </span>{{ $labels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}</span>
