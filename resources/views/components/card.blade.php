@props(['title' => null, 'description' => null])
<section {{ $attributes->class('panel') }}>
    @if ($title)
        <div class="panel-heading">
            <h2>{{ $title }}</h2>
            @if ($description)
                <p class="muted">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="panel-body">{{ $slot }}</div>
</section>
