@props(['stat'])

<div {{ $attributes->merge(['class' => 'stat']) }} data-reveal-card>
    <p class="stat__value">
        @if (! empty($stat['prefix']))<span class="stat__affix">{{ $stat['prefix'] }}</span>@endif<span data-count="{{ $stat['value'] }}">{{ $stat['value'] }}</span>@if (! empty($stat['suffix']))<span class="stat__affix">{{ $stat['suffix'] }}</span>@endif
    </p>
    <p class="stat__label">{{ $stat['label'] }}</p>
    @if (! empty($stat['context']))
        <p class="stat__context">{{ $stat['context'] }}</p>
    @endif
</div>
