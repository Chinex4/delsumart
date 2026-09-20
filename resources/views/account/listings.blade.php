@extends('layouts.student')
@section('title', 'My listings')
@section('content')
    <x-page-header title="Your listings, all in one place."
        description="Keep track of what you've listed and what's found a new home." eyebrow="SELLER WORKSPACE">
        <x-button :href="route('listings.create')" icon="plus">Create listing</x-button>
    </x-page-header>
    @if ($listings->isEmpty())<x-card>
            <x-empty title="Your seller story starts here"
                description="Have something useful to pass on? Your campus is a great place to start.">
                <x-button :href="route('listings.create')">Create your first listing</x-button>
            </x-empty>
        </x-card>
    @else
        <x-table label="Your listings">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Listed on</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listings as $listing)
                    <tr>
                        <td>
                            <div class="flex gap-3 items-center">
                                @if ($listing->images->first())
                                    <img class="w-12 h-12 rounded object-cover"
                                        src="{{ asset('storage/' . $listing->images->first()->path) }}"
                                        alt="{{ $listing->title }}">
                                @else
                                    <span class="stat-icon">
                                        <x-icon name="bag" />
                                    </span>
                                @endif
                                <div>
                                    <strong>{{ $listing->title }}</strong>
                                    <small>{{ $listing->category }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap">₦{{ number_format($listing->price, 2) }}</td>
                        <td>
                            <x-badge :status="$listing->status" />
                        </td>
                        <td class="whitespace-nowrap">{{ $listing->created_at->format('d M Y') }}</td>
                        <td>
                            <a class="text-link" href="{{ route('listings.show', $listing) }}">View / manage <x-icon
                                    name="arrow" size="14" />
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="pagination">{{ $listings->links() }}</div>
    @endif
@endsection
