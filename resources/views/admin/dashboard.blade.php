@extends('layouts.admin')
@section('title', 'Trust & safety overview')
@section('content')
    <x-page-header title="Trust & safety overview" eyebrow="DELSUMART OPERATIONS"
        description="A clear view of your campus marketplace and the people who keep it moving." />
    <div class="stats-grid">
        @foreach (config('ui.admin_stats') as $key => $item)
            <x-stat :label="$item[0]" :value="$stats[$key]" :icon="$item[1]" />
        @endforeach
    </div>
    <div class="detail-grid">
        <div class="stack">
            <x-card title="Students ready for review"
                description="Check both documents before making a verification decision.">
                @forelse($pending as $v)
                    <div class="flex items-center gap-3 py-4 border-b border-slate-100 last:border-0">
                        <x-avatar :user="$v->user" />
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold">{{ $v->full_name }}</p>
                            <p class="field-hint">{{ $v->matric_no }} · {{ $v->programme }}</p>
                        </div>
                        <a class="text-link" href="{{ route('admin.verifications.show', $v) }}">Review <x-icon
                                name="arrow" size="16" />
                        </a>
                    </div>
                @empty
                    <x-empty title="The queue is clear" description="New student submissions will appear here." />
                @endforelse
                <a class="text-link mt-5" href="{{ route('admin.verifications') }}">View review queue →</a>
            </x-card>
            <x-card title="Recent transactions">
                @forelse($recentTransactions as $tx)
                    <div class="flex justify-between gap-3 py-4 border-b border-slate-100 last:border-0">
                        <div>
                            <p class="font-semibold text-sm">{{ $tx->listing->title }}</p>
                            <p class="field-hint">{{ $tx->buyer->name }} → {{ $tx->seller->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold mb-2">₦{{ number_format($tx->amount, 2) }}</p>
                            <x-badge :status="$tx->status" />
                        </div>
                    </div>
                @empty
                    <x-empty title="No transactions yet" />
                @endforelse
                <a class="text-link mt-5" href="{{ route('admin.transactions') }}">
                    All transactions →</a>
            </x-card>
        </div>
        <div class="stack">
            <x-card title="Review priorities">
                <div class="form-stack">
                    <x-button :href="route('admin.disputes')" variant="secondary" icon="message">Resolve
                        disputes</x-button>
                    <x-button :href="route('admin.flags')" variant="secondary" icon="flag">Review fraud
                        signals</x-button>
                    <x-button :href="route('admin.payouts')" variant="secondary" icon="wallet">Manage
                        payouts</x-button>
                </div>
            </x-card>
            <x-card title="Latest audit activity">
                @forelse($recentAudits as $log)
                    <div class="py-3 border-b border-slate-100 last:border-0">
                        <p class="text-sm font-semibold">{{ Str::headline($log->action_type) }}</p>
                        <p class="field-hint">{{ $log->admin?->name ?? 'System' }} ·
                            {{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="muted text-sm">No administrative activity recorded.</p>
                @endforelse
                <a class="text-link mt-5" href="{{ route('admin.audits') }}">Explore audit log →</a>
            </x-card>
        </div>
    </div>
@endsection
