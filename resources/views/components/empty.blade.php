@props(['title' => 'Nothing here yet', 'description' => 'Your activity will appear here.', 'icon' => 'bag'])
<div {{ $attributes->class('empty-state') }}>
    <span class="empty-icon">
        <x-icon :name="$icon" size="30" />
    </span>
    <h2>{{ $title }}</h2>
    <p>{{ $description }}</p>{{ $slot }}
</div>
