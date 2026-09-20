@props(['tone' => 'info', 'title' => null])
<div {{ $attributes->class(['alert', 'alert-' . $tone]) }} role="{{ $tone === 'danger' ? 'alert' : 'status' }}">
    <x-icon :name="$tone === 'success' ? 'check' : 'shield'" />
    <div>
        @if ($title)
            <strong>{{ $title }}</strong>
        @endif
        <div>{{ $slot }}</div>
    </div>
</div>
