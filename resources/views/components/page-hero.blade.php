@props(['eyebrow', 'title', 'lead' => null, 'variant' => 'default', 'image' => null])

@if ($image)
    {{-- With an uploaded hero image: copy on the left, framed image on the right (stacked on phones). --}}
    <section {{ $attributes->merge(['class' => 'page-hero page-hero--media page-hero--'.$variant]) }} data-page-hero>
        <div class="container-xl page-hero__inner page-hero__grid">
            <div class="page-hero__copy">
                <p class="eyebrow" data-hero-item>{{ $eyebrow }}</p>
                <h1 class="page-hero__title" data-split="hero">{!! $title !!}</h1>
                @if ($lead)
                    <p class="page-hero__lead" data-hero-item>{{ $lead }}</p>
                @endif
                @if (trim($slot))
                    <div class="page-hero__actions" data-hero-item>{{ $slot }}</div>
                @endif
            </div>
            <figure class="page-hero__media" data-hero-item>
                <img src="{{ media_url($image) }}" alt="" decoding="async">
            </figure>
        </div>
    </section>
@else
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
@endif
