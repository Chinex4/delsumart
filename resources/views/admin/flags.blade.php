@extends('layouts.admin')
@section('title', 'Fraud monitoring')
@section('content')
    <x-page-header title="Fraud monitoring" eyebrow="REVIEW SIGNALS"
        description="Explainable risk indicators help prioritize review. A flag is not proof of misconduct." />
    <form method="GET" class="filter-bar">
        <x-field name="status" label="Review status" type="select">
            <option value="">All statuses</option>
            @foreach (['open', 'reviewed', 'dismissed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </x-field>
        <x-field name="risk" label="Risk level" type="select">
            <option value="">All levels</option>
            @foreach (['low', 'medium', 'high'] as $level)
                <option value="{{ $level }}" @selected(request('risk') === $level)>{{ ucfirst($level) }}</option>
            @endforeach
        </x-field>
        <x-button variant="secondary">Filter</x-button>
        <a class="text-link mb-3" href="{{ route('admin.flags') }}">Reset</a>
    </form>
    <div class="stack">
        @forelse($flags as $flag)
            <x-card>
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="eyebrow">{{ ucfirst($flag->related_type) }} #{{ $flag->related_id }}</p>
                        <h2 class="text-lg">
                            {{ $flag->related_type === 'user' ? $accounts->get($flag->related_id)?->name ?? 'Student account' : 'Marketplace activity' }}
                        </h2>
                        <p class="field-hint">Flagged {{ $flag->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold mb-2">{{ $flag->risk_score }}<span class="muted text-sm"> / 100</span>
                        </p>
                        <x-badge :status="$flag->risk_score >= 60 ? 'high' : ($flag->risk_score >= 30 ? 'medium' : 'low')" />
                    </div>
                </div>
                <p class="text-sm muted mt-5">{{ $flag->flag_reason }}</p>
                <div class="flex flex-wrap items-center gap-3 mt-5">
                    <x-badge :status="$flag->status" />
                    @if ($flag->related_type === 'user' && $accounts->has($flag->related_id))
                        <a class="text-link" href="{{ route('admin.students.show', $flag->related_id) }}">Review student
                            →</a>
                    @endif
                </div>
                @if ($flag->status === 'open')
                    <form method="POST" action="{{ route('admin.flags.review', $flag) }}"
                        class="flex flex-wrap gap-3 mt-5">
                        @csrf @method('PATCH')
                        <x-button name="status" value="reviewed" variant="secondary">Mark reviewed</x-button>
                        <x-button name="status" value="dismissed" variant="secondary">Dismiss flag</x-button>
                    </form>
                @elseif($flag->reviewed_at)
                    <p class="field-hint mt-3">Reviewed {{ $flag->reviewed_at->format('d M Y, H:i') }}</p>
                @endif
            </x-card>
        @empty
            <x-card>
                <x-empty title="No matching flags" description="There are no review signals matching these filters."
                    icon="shield" />
            </x-card>
        @endforelse
    </div>
    <div class="pagination">{{ $flags->links() }}</div>
@endsection
