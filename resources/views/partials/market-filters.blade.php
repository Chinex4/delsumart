<form method="GET" action="{{ route('listings.index') }}" class="form-stack">
    <input type="hidden" name="q" value="{{ request('q') }}">
    <input type="hidden" name="sort" value="{{ request('sort', 'latest') }}">
    <div class="field">
        <label for="category-{{ $suffix }}">Category</label>
        <select class="input" name="category" id="category-{{ $suffix }}">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="min-{{ $suffix }}">Minimum price (₦)</label>
        <input class="input" id="min-{{ $suffix }}" type="number" name="min_price"
            value="{{ request('min_price') }}" min="0" placeholder="0">
    </div>
    <div class="field">
        <label for="max-{{ $suffix }}">Maximum price (₦)</label>
        <input class="input" id="max-{{ $suffix }}" type="number" name="max_price"
            value="{{ request('max_price') }}" min="0" placeholder="Any price">
    </div>
    <x-button icon="filter">Apply filters</x-button>
    <a href="{{ route('listings.index') }}" class="text-link justify-center">Reset all filters</a>
</form>
