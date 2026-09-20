@extends('layouts.admin')
@section('title', 'Student verification')
@section('content')
    <x-page-header title="Student verification" eyebrow="TRUST STARTS HERE"
        description="Review student identities with care. Documents stay private throughout the process." />
    <form method="GET" class="filter-bar">
        <x-field name="status" label="Verification status" type="select">
            <option value="">All submissions</option>
            @foreach (['pending', 'verified', 'rejected'] as $state)
                <option value="{{ $state }}" @selected(request('status') === $state)>{{ ucfirst($state) }}</option>
            @endforeach
        </x-field>
        <x-button variant="secondary">Filter</x-button>
        <a class="text-link mb-3" href="{{ route('admin.verifications') }}">Reset</a>
    </form>
    <x-table label="Student verification queue">
        <thead>
            <tr>
                <th>Student</th>
                <th>Programme</th>
                <th>Submitted</th>
                <th>Status</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            @forelse($verifications as $v)
                <tr>
                    <td>
                        <strong>{{ $v->full_name }}</strong>
                        <p class="field-hint">{{ $v->matric_no }}</p>
                    </td>
                    <td>{{ $v->programme }}<p class="field-hint">Level {{ $v->level }}</p>
                    </td>
                    <td>{{ $v->created_at->format('d M Y') }}<p class="field-hint">{{ $v->resubmission_count }}
                            resubmissions</p>
                    </td>
                    <td>
                        <x-badge :status="$v->verification_status" />
                    </td>
                    <td>
                        <a class="text-link" href="{{ route('admin.verifications.show', $v) }}">View documents →</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty title="No submissions found"
                            description="Try another status or come back when students submit their documents." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
    <div class="pagination">{{ $verifications->links() }}</div>
@endsection
