@props(['name', 'size' => 24])

@php
    // Stroke icons on a 24×24 grid.
    $paths = [
        'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.2" fill="currentColor"/>',
        'cart' => '<path d="M3 4h2l2.2 10.2a1.5 1.5 0 0 0 1.5 1.2h8.6a1.5 1.5 0 0 0 1.4-1.1L21 8H6.2"/><circle cx="9.5" cy="19.5" r="1.3"/><circle cx="17" cy="19.5" r="1.3"/>',
        'funnel' => '<path d="M3.5 5h17l-6.5 7.5v5.5l-4 2v-7.5z"/>',
        'repeat' => '<path d="M4 11V9a3 3 0 0 1 3-3h12m-3-3 3 3-3 3"/><path d="M20 13v2a3 3 0 0 1-3 3H5m3 3-3-3 3-3"/>',
        'layers' => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
        'pulse' => '<path d="M3 12h4l2.5-6 5 12 2.5-6h4"/>',
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
        'spark' => '<path d="M12 3v4m0 10v4M3 12h4m10 0h4M6 6l2.5 2.5m7 7L18 18M6 18l2.5-2.5m7-7L18 6"/>',
        'display' => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8m-4-4v4"/>',
        'gauge' => '<path d="M4.5 17a8.5 8.5 0 1 1 15 0"/><path d="m12 13 4-5"/><circle cx="12" cy="13" r="1.2" fill="currentColor"/>',
        'bid' => '<path d="M4 20h10M6.5 13.5l5-5m-3-3 6 6m-1.5-7.5 4.5 4.5-2 2-4.5-4.5z"/><path d="m6.5 13.5-1 1 2 2 1-1"/>',
        'tag' => '<path d="M3.5 12.3V4.5a1 1 0 0 1 1-1h7.8l8.2 8.2a1 1 0 0 1 0 1.4l-7.8 7.8a1 1 0 0 1-1.4 0z"/><circle cx="8" cy="8" r="1.4"/>',
        'compass' => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>',
        'chart' => '<path d="M4 20V4m0 16h16"/><path d="M8 16v-4m4 4V9m4 7v-6m4 6V6"/>',
        'meta' => '<path d="M3 15.5c0-4 2-8 4.5-8 3.5 0 5.5 9 9 9 2 0 3.5-1.7 3.5-4.5 0-4-2-8-4.5-8-3.5 0-5.5 9-9 9C4.3 13 3 14 3 15.5z"/>',
        'google' => '<path d="M20.5 12.2c0-.7-.1-1.3-.2-1.9H12v3.6h4.8a4.2 4.2 0 0 1-1.8 2.7"/><path d="M15 16.6A6 6 0 0 1 6.3 13.9M6.2 10.1A6 6 0 0 1 15.9 7.3l2.6-2.6A9.5 9.5 0 0 0 3.9 8.3M3.9 15.7a9.5 9.5 0 0 0 14.8 2.9"/>',
        'tiktok' => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3c.5 2.6 2.4 4.4 5 4.6"/>',
        'arrow-right' => '<path d="M5 12h14m-5-5 5 5-5 5"/>',
        'arrow-up-right' => '<path d="M7 17 17 7M9 7h8v8"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6.5 8.5-6.5"/>',
        'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10.5V16m0-8v.01M12 16v-5.5m0 2.5a2.5 2.5 0 0 1 5 0V16"/>',
        'phone' => '<path d="M5 4h3.5l1.5 4-2 1.5a11 11 0 0 0 6.5 6.5l1.5-2 4 1.5V19a1 1 0 0 1-1 1A16 16 0 0 1 4 5a1 1 0 0 1 1-1z"/>',
        'map-pin' => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/>',
        'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18.5 14a6.5 6.5 0 0 1 3 6"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2M3 12.5h18"/>',
        'graduation' => '<path d="m12 4 10 5-10 5L2 9z"/><path d="M6 11v5c3 2.5 9 2.5 12 0v-5M22 9v5"/>',
        'send' => '<path d="M21 3 10.5 13.5M21 3l-6.5 18-4-7.5L3 9.5z"/>',
        'x' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'expand' => '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/>',
        'columns' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M12 4v16"/>',
        'slider' => '<path d="M12 3v18"/><path d="m8 9-3 3 3 3M16 9l3 3-3 3"/>',
        'image' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 17-5-5-9 8"/>',
        'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
        'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
        'trash' => '<path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/>',
        'edit' => '<path d="M4 20h4L19 9l-4-4L4 16z"/>',
        'alert' =>'<path d="M12 3.5 2.5 20h19z"/><path d="M12 10v4m0 3v.01"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'icon']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $paths[$name] ?? $paths['spark'] !!}</svg>
