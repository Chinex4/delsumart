@extends('layouts.student')
@section('title', 'Create a listing')
@section('content')
    <x-page-header title="Someone on campus needs this."
        description="A clear description and good photos help your item find its next home." eyebrow="CREATE A LISTING" />
    <div class="detail-grid">
        <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="stack">
            @csrf
            <x-card title="01 · The essentials" description="Tell your campus what you're selling.">
                <div class="form-stack">
                    <x-field name="title" label="Listing title" placeholder="e.g. HP EliteBook 840 G7, 8 GB RAM"
                        maxlength="120" required />
                    <div class="form-grid">
                        <x-field name="category" label="Category" type="select" required>
                            <option value="">Choose a category</option>
                            @foreach (config('marketplace.categories') as $category => $icon)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}
                                </option>
                            @endforeach
                        </x-field>
                        <x-field name="price" label="Price (₦)" type="number" min="100" max="10000000"
                            step="0.01" placeholder="0.00" hint="Between ₦100 and ₦10,000,000." required />
                    </div>
                    <x-field name="description" label="About your item" type="textarea" rows="6" maxlength="3000"
                        hint="Describe its condition, useful features, and any defects honestly." required />
                </div>
            </x-card>
            <x-card title="02 · Add your photos" description="Let students see exactly what they're getting.">
                <div class="form-stack">
                    <x-field name="images[]" label="Product images" type="file" accept="image/*" multiple
                        data-image-upload="listing-previews"
                        hint="Up to 5 images, maximum 4 MB each. Use photos of the actual item." />
                    <div id="listing-previews" class="upload-preview" aria-live="polite">
                    </div>
                </div>
            </x-card>
            <div class="flex flex-wrap gap-3">
                <x-button icon="plus">Publish listing</x-button>
                <x-button :href="route('account.listings')" variant="secondary">Cancel</x-button>
            </div>
        </form>
        <aside class="stack content-start">
            <x-card title="A listing that stands out">
                <ul class="space-y-5 text-xs muted">
                    <li>
                        <strong class="block text-navy mb-1">Make the title specific</strong>Include the brand, model or
                        book title.
                    </li>
                    <li>
                        <strong class="block text-navy mb-1">Be honest about condition</strong>Great transactions start with
                        clear expectations.
                    </li>
                    <li>
                        <strong class="block text-navy mb-1">Use bright, clear photos</strong>A few angles help buyers make
                        an informed choice.
                    </li>
                </ul>
            </x-card>
            <x-alert title="Keep it on DelsuMart">Use the protected payment flow and keep transaction records in
                your workspace.</x-alert>
        </aside>
    </div>
@endsection
