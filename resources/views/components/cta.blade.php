@props(['title' => null, 'text' => null])

@php
    // Page-specific title/text when given (and not empty); otherwise the shared defaults.
    $cta = config('content.site.cta');
    $title = filled($title) ? $title : $cta['title'];
    $text = filled($text) ? $text : $cta['text'];
@endphp

<section class="cta-band">
    <div class="container-xl">
        <div class="cta-band__panel" data-reveal-card>
            <div class="cta-band__orb" aria-hidden="true"></div>
            <p class="eyebrow">{{ txt($cta['eyebrow']) }}</p>
            <h2 class="cta-band__title" data-split>{{ rich($title) }}</h2>
            <p class="cta-band__text">{{ txt($text) }}</p>
            <div class="cta-band__actions">
                <x-button :href="route('contact')" icon="arrow-right">{{ txt($cta['primary']) }}</x-button>
                <x-button :href="config('portfolio.person.linkedin')" variant="ghost" icon="arrow-up-right" target="_blank" rel="noopener">{{ txt($cta['secondary']) }}</x-button>
            </div>
        </div>
    </div>
</section>
