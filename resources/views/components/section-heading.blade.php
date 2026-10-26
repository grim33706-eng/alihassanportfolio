@props(['eyebrow' => null, 'title', 'lead' => null, 'align' => 'start'])

<div {{ $attributes->merge(['class' => 'section-heading section-heading--'.$align]) }}>
    @if ($eyebrow)
        <p class="eyebrow" data-reveal>{{ $eyebrow }}</p>
    @endif
    <h2 class="section-title" data-split>{!! $title !!}</h2>
    @if ($lead)
        <p class="section-lead" data-reveal>{{ $lead }}</p>
    @endif
</div>
