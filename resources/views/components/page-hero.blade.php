@props(['eyebrow', 'title', 'lead' => null, 'variant' => 'default'])

<section {{ $attributes->merge(['class' => 'page-hero page-hero--'.$variant]) }} data-page-hero>
    <div class="page-hero__visual" aria-hidden="true">
        {{ $visual ?? '' }}
    </div>
    <div class="container-xl page-hero__inner">
        <p class="eyebrow" data-hero-item>{{ $eyebrow }}</p>
        <h1 class="page-hero__title" data-split="hero">{!! $title !!}</h1>
        @if ($lead)
            <p class="page-hero__lead" data-hero-item>{{ $lead }}</p>
        @endif
        @if (trim($slot))
            <div class="page-hero__actions" data-hero-item>{{ $slot }}</div>
        @endif
    </div>
</section>
