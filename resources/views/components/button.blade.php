@props(['href' => null, 'variant' => 'primary', 'icon' => null])
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['btn', 'btn-' . $variant]) }}>
        @if ($icon)
            <x-icon :name="$icon" size="18" />
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->class(['btn', 'btn-' . $variant])->merge(['type' => 'submit']) }}>
        @if ($icon)
            <x-icon :name="$icon" size="18" />
        @endif
        {{ $slot }}
    </button>
@endif
