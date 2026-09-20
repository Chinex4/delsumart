@props(['title', 'description' => null, 'eyebrow' => null])
<header {{ $attributes->class('page-header') }}>
    <div>
        @if ($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1>
            {{ $title }}</h1>
        @if ($description)
            <p class="muted">{{ $description }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="header-actions">{{ $slot }}</div>
    @endif
</header>
