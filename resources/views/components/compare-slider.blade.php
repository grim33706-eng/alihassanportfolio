@props(['caseStudy'])

@php
    $before = $caseStudy->beforeImages()->first();
    $after = $caseStudy->afterImages()->first();
    $group = 'case-'.$caseStudy->id;
@endphp

<div {{ $attributes->merge(['class' => 'compare']) }} data-compare style="--pos: 50%">
    <div class="compare__layer compare__layer--after">
        @if ($after)
            <img src="{{ $after->url() }}" alt="After: {{ $after->caption ?: $caseStudy->title }}" loading="lazy" decoding="async"
                @if ($after->width) width="{{ $after->width }}" height="{{ $after->height }}" @endif>
        @else
            @include('partials.screenshot-placeholder', ['stage' => 'after'])
        @endif
    </div>
    <div class="compare__layer compare__layer--before">
        @if ($before)
            <img src="{{ $before->url() }}" alt="Before: {{ $before->caption ?: $caseStudy->title }}" loading="lazy" decoding="async"
                @if ($before->width) width="{{ $before->width }}" height="{{ $before->height }}" @endif>
        @else
            @include('partials.screenshot-placeholder', ['stage' => 'before'])
        @endif
    </div>

    <span class="compare__label compare__label--before">Before</span>
    <span class="compare__label compare__label--after">After</span>

    <span class="compare__divider" aria-hidden="true">
        <span class="compare__knob"><x-icon name="slider" size="20" /></span>
    </span>

    {{-- A native range input gives mouse, touch and keyboard control for free. --}}
    <input type="range" class="compare__range" min="0" max="100" step="0.5" value="50"
        aria-label="Comparison slider: move left to see more of the after screenshot, right to see more of before"
        aria-valuetext="50% before" data-compare-range>

    @if ($before || $after)
        <button type="button" class="compare__expand" data-lightbox-open="{{ $group }}" aria-label="View screenshots full size">
            <x-icon name="expand" size="18" />
        </button>
    @endif
</div>
