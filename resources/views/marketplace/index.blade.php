@extends('layouts.app')
@section('title', 'Explore the marketplace')
@section('content')
    <div class="container section">
        <x-page-header title="Find your next campus essential."
            description="Good finds from your student community. A little closer to home." eyebrow="THE DELSU MARKETPLACE">
            <x-button :href="route('listings.create')" icon="plus">Sell an item</x-button>
        </x-page-header>
        <form action="{{ route('listings.index') }}" method="GET" class="search-box">
            <label for="market-search" class="sr-only">Search marketplace</label>
            <input id="market-search" name="q" value="{{ request('q') }}"
                placeholder="Search laptops, phones, books and more…">
            @foreach (['category', 'min_price', 'max_price', 'sort'] as $filter)
                <input type="hidden" name="{{ $filter }}" value="{{ request($filter) }}">
            @endforeach
            <x-button icon="search">
                Search</x-button>
        </form>
        <div class="market-layout">
            <aside class="market-filters">
                <h2>Refine your search</h2>@include('partials.market-filters', ['suffix' => 'desktop'])
            </aside>
            <div>
                <div class="market-toolbar">
                    <p>
                        <strong class="text-navy">{{ number_format($listings->total()) }}</strong>
                        {{ Str::plural('result', $listings->total()) }}{{ request('q') ? ' for “' . request('q') . '”' : ' around campus' }}
                    </p>
                    <button class="btn btn-secondary mobile-toggle" type="button" data-dialog="market-filters">
                        <x-icon name="filter" size="16" /> Filters</button>
                    <form class="flex items-center gap-2" action="{{ route('listings.index') }}">
                        @foreach (['q', 'category', 'min_price', 'max_price'] as $filter)
                            <input type="hidden" name="{{ $filter }}" value="{{ request($filter) }}">
                        @endforeach
                        <label class="sr-only" for="sort">
                            Sort listings</label>
                        <select class="input" id="sort" name="sort">
                            @foreach (config('ui.sort_options') as $value => $label)
                                <option value="{{ $value }}" @selected(request('sort', 'latest') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <button class="icon-button" aria-label="Apply sort">
                            <x-icon name="arrow" size="16" />
                        </button>
                    </form>
                </div>
                <div class="listing-grid">
                    @forelse($listings as $listing)
                        <x-listing-card :listing="$listing" />
                    @empty
                        <x-empty title="No finds this time"
                            description="Try another search or clear a few filters. New campus finds could appear soon."
                            icon="search">
                            <x-button :href="route('listings.index')" variant="secondary">Clear
                                filters</x-button>
                        </x-empty>
                    @endforelse
                </div>
                <div class="pagination">{{ $listings->links() }}</div>
            </div>
        </div>
    </div>
    <x-modal id="market-filters" title="Filter marketplace">@include('partials.market-filters', ['suffix' => 'mobile'])</x-modal>
@endsection
