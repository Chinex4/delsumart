@extends('layouts.admin')
@section('title', 'Audit logs')
@section('content')
    <x-page-header title="Audit logs" eyebrow="ACCOUNTABILITY"
        description="A record of administrative decisions and the reasons behind them." />
    <form method="GET" class="filter-bar">
        <x-field name="action" label="Find an action" :value="request('action')" placeholder="e.g. kyc_verified" />
        <x-button variant="secondary">Search</x-button>
        <a class="text-link mb-3" href="{{ route('admin.audits') }}">Reset</a>
    </form>
    <x-table label="Administrative audit log">
        <thead>
            <tr>
                <th>When</th>
                <th>Actor</th>
                <th>Action</th>
                <th>Resource</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="whitespace-nowrap">{{ $log->created_at->format('d M Y') }}<p class="field-hint">
                            {{ $log->created_at->format('H:i:s') }}</p>
                    </td>
                    <td>{{ $log->admin?->name ?? 'System' }}</td>
                    <td>
                        <strong>{{ Str::headline($log->action_type) }}</strong>
                    </td>
                    <td>{{ Str::headline($log->target_type) }} #{{ $log->target_id }}</td>
                    <td class="max-w-md whitespace-normal">{{ $log->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty title="No matching activity" description="Try a different action or reset your search." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
    <div class="pagination">{{ $logs->links() }}</div>
@endsection
