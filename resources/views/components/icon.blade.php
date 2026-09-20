@props(['name' => 'arrow', 'size' => 20])
@php
    $paths = [
        'arrow' => 'M5 12h14m-6-6 6 6-6 6',
        'chevron' => 'm9 5 7 7-7 7',
        'search' => 'm21 21-5-5M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0',
        'shield' => 'M12 3 3 7v5c0 5 9 9 9 9s9-4 9-9V7l-9-4Zm-4 9 3 3 5-6',
        'bag' => 'M5 7h14l1 14H4L5 7Zm3 0V5a4 4 0 0 1 8 0v2',
        'grid' => 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z',
        'phone' => 'M8 2h8a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm2 16h4',
        'laptop' => 'M4 4h16v12H4zM2 20h20l-2-4H4l-2 4Z',
        'book' => 'M12 5v16M3 3c4-1 6 0 9 2 3-2 5-3 9-2v16c-4-1-6 0-9 2-3-2-5-3-9-2V3Z',
        'home' => 'm3 10 9-7 9 7v11H3V10Zm6 11v-8h6v8',
        'users' =>
            'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.87M9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm8 .13a4 4 0 0 1 0 7.75',
        'user' => 'M20 21v-2a7 7 0 0 0-14 0v2M12 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8',
        'check' => 'm5 12 4 4L19 6',
        'plus' => 'M12 5v14M5 12h14',
        'menu' => 'M4 6h16M4 12h16M4 18h16',
        'close' => 'm6 6 12 12M6 18 18 6',
        'logout' => 'M9 4H4v16h5m5-14 6 6-6 6M8 12h12',
        'clock' => 'M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0',
        'wallet' => 'M3 6h16v14H3V6Zm0 0V3h14v3m4 5h-7v5h7v-5Z',
        'message' => 'M21 11a9 9 0 0 1-9 9H3l1-5a9 9 0 1 1 17-4ZM8 10h8M8 14h5',
        'file' => 'M14 2H4v20h16V8l-6-6Zm0 0v6h6M8 13h8M8 17h6',
        'lock' => 'M5 10h14v11H5V10Zm3 0V6a4 4 0 0 1 8 0v4m-4 5v2',
        'flag' => 'M4 22V3c6-4 10 4 16 0v12c-6 4-10-4-16 0',
        'filter' => 'M4 7h16M7 12h10M10 17h4',
        'image' => 'M3 3h18v18H3V3Zm0 14 5-5 4 4 3-3 6 6M8 7h.01',
        'upload' => 'M12 16V3m-5 5 5-5 5 5M4 16v5h16v-5',
        'location' => 'M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Zm-8-3a3 3 0 1 0 0 6 3 3 0 0 0 0-6',
        'mail' => 'M3 5h18v14H3V5Zm0 0 9 8 9-8',
        'eye' => 'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Zm10-3a3 3 0 1 0 0 6 3 3 0 0 0 0-6',
    ];
@endphp
<svg {{ $attributes->class('icon') }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
    fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="{{ $paths[$name] ?? $paths['grid'] }}" />
</svg>
