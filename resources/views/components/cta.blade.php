@props([
    'title' => 'Ready to scale what\'s working?',
    'text' => 'Whether you\'re hiring for a performance marketing role or need someone to run your Meta and Google Ads, let\'s talk about your goals.',
])

<section class="cta-band">
    <div class="container-xl">
        <div class="cta-band__panel" data-reveal-card>
            <div class="cta-band__orb" aria-hidden="true"></div>
            <p class="eyebrow">Get in touch</p>
            <h2 class="cta-band__title" data-split>{{ $title }}</h2>
            <p class="cta-band__text">{{ $text }}</p>
            <div class="cta-band__actions">
                <x-button :href="route('contact')" icon="arrow-right">Contact me</x-button>
                <x-button :href="config('portfolio.person.linkedin')" variant="ghost" icon="arrow-up-right" target="_blank" rel="noopener">View LinkedIn</x-button>
            </div>
        </div>
    </div>
</section>
