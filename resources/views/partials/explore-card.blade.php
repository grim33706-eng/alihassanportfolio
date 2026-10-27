{{-- Closing card of the home "Selected work" slider. Expects $totals (projects, markets, platforms). --}}
@php
    $explore = config('content.home.explore');
@endphp
<a href="{{ route('portfolio') }}" class="explore-card project-slider__item" aria-label="Explore all {{ $totals['projects'] }} projects">
    <span class="explore-card__glow" aria-hidden="true"></span>

    <span class="explore-card__top">
        <span class="explore-card__eyebrow">{{ txt($explore['eyebrow']) }}</span>
        <span class="explore-card__count">{{ $totals['projects'] }} projects</span>
    </span>

    {{-- A small stack of mini project cards that fans out on hover. --}}
    <span class="explore-card__stack" aria-hidden="true">
        @foreach (config('portfolio.categories') as $key => $label)
            <span class="mini-card mini-card--{{ $key }}">
                <span class="mini-card__label">{{ $label }}</span>
                <svg viewBox="0 0 120 50" preserveAspectRatio="none">
                    <path class="mini-card__area" d="M0 42 C 18 40, 26 33, 40 32 S 64 24, 76 20 S 100 9, 120 5 V50 H0 Z" />
                    <path class="mini-card__line" d="M0 42 C 18 40, 26 33, 40 32 S 64 24, 76 20 S 100 9, 120 5" />
                </svg>
            </span>
        @endforeach
    </span>

    <span class="explore-card__body">
        <span class="explore-card__title">{{ txt($explore['title']) }}</span>
        <span class="explore-card__text">{{ txt($explore['text']) }}</span>
    </span>

    <span class="explore-card__stats">
        <span><strong>{{ $totals['projects'] }}</strong> projects</span>
        <span><strong>{{ $totals['markets'] }}</strong> markets</span>
        <span><strong>{{ $totals['platforms'] }}</strong> platforms</span>
    </span>

    <span class="explore-card__cta">
        <span class="explore-card__cta-label">{{ txt($explore['button']) }}</span>
        <span class="explore-card__arrow"><x-icon name="arrow-right" size="20" /></span>
    </span>
</a>
