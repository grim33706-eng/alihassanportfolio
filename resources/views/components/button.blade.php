@props(['href' => null, 'variant' => 'primary', 'size' => null, 'icon' => null, 'type' => 'button'])

@php
    $classes = 'btn-ui btn-ui--'.$variant.($size ? ' btn-ui--'.$size : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <span class="btn-ui__label">{{ $slot }}</span>
        @if ($icon)<x-icon :name="$icon" size="18" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        <span class="btn-ui__label">{{ $slot }}</span>
        @if ($icon)<x-icon :name="$icon" size="18" />@endif
    </button>
@endif
