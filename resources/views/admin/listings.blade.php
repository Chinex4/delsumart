@extends('layouts.admin')
@section('title', 'Marketplace listings')
@section('content')
    <x-page-header title="Marketplace listings" eyebrow="CAMPUS COMMERCE"
        description="Review real listings and their current marketplace status." />
    <form method="GET" class="filter-bar">
        <x-field name="q" label="Search listings" :value="request('q')" placeholder="Listing title" />
        <x-field name="status" label="Listing status" type="select">
            <option value="">All statuses</option>
            @foreach (['active', 'sold', 'removed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </x-field>
        <x-button variant="secondary">Search</x-button>
        <a class="text-link mb-3" href="{{ route('admin.listings') }}">Reset</a>
    </form>
    <x-table label="Marketplace listings">
        <thead>
            <tr>
                <th>Listing</th>
                <th>Seller</th>
                <th>Price</th>
                <th>Status</th>
                <th>Created</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listings as $listing)
                <tr>
                    <td>
                        <strong>{{ $listing->title }}</strong>
                        <p class="field-hint">{{ $listing->category }}</p>
                    </td>
                    <td>{{ $listing->seller->name }}</td>
                    <td class="font-semibold">₦{{ number_format($listing->price, 2) }}</td>
                    <td>
                        <x-badge :status="$listing->status" />
                    </td>
                    <td>{{ $listing->created_at->format('d M Y') }}</td>
                    <td>
                        <a class="text-link" href="{{ route('listings.show', $listing) }}">View listing →</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty title="No matching listings" description="Try another title or reset the status filter." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>
    <div class="pagination">{{ $listings->links() }}</div>
@endsection
