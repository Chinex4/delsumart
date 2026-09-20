@extends('layouts.admin')
@section('title', 'Students')
@section('content')
    <x-page-header title="Students" eyebrow="THE CAMPUS COMMUNITY"
        description="Find student accounts, review verification and understand marketplace activity." />
    <form method="GET" class="filter-bar">
        <x-field name="q" label="Find a student" :value="request('q')" placeholder="Name, email or matric number" />
        <x-field name="status" label="Verification" type="select">
            <option value="">All statuses</option>
            @foreach (['pending', 'verified', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </x-field>
        <x-field name="account_status" label="Account" type="select">
            <option value="">All accounts</option>
            @foreach (['active', 'suspended'] as $status)
                <option value="{{ $status }}" @selected(request('account_status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </x-field>
        <x-button variant="secondary">Search</x-button>
        <a class="text-link mb-3" href="{{ route('admin.students') }}">Reset</a>
    </form>
    <x-table label="Student accounts">
        <thead>
            <tr>
                <th>Student</th>
                <th>Programme</th>
                <th>Verification</th>
                <th>Account</th>
                <th>Joined</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $s)
                <tr>
                    <td>
                        <strong>{{ $s->name }}</strong>
                        <p class="field-hint">{{ $s->matric_no }}</p>
                        <p class="field-hint">{{ $s->email }}</p>
                    </td>
                    <td>{{ $s->programme }}<p class="field-hint">Level {{ $s->level }}</p>
                    </td>
                    <td>
                        <x-badge :status="$s->verification?->verification_status ?? 'not_submitted'" />
                    </td>
                    <td>
                        <x-badge :status="$s->account_status" />
                    </td>
                    <td>{{ $s->created_at->format('d M Y') }}</td>
                    <td>
                        <a class="text-link" href="{{ route('admin.students.show', $s) }}">View student →</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty title="No students found" description="Try another name or reset your filters." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
    <div class="pagination">{{ $students->links() }}</div>
@endsection
