{{--
    Home "Selected work" showcase: a centred feature slide with neighbours peeking in,
    ending on an "explore the portfolio" slide.
    Expects $projects (featured projects) and $totals (projects, markets, platforms).
    Without GSAP (or with reduced motion) it falls back to a native swipeable scroller.
--}}
@php
    // Three curve shapes so neighbouring slides don't look identical. Illustrative only.
    $curves = [
        ['d' => 'M0 300 C 80 290, 120 252, 190 242 S 300 190, 350 172 S 470 92, 560 56', 'end' => 56, 'y' => 16.5],
        ['d' => 'M0 292 C 70 272, 140 282, 210 232 S 320 200, 370 152 S 480 112, 560 74', 'end' => 74, 'y' => 21.8],
        ['d' => 'M0 312 C 90 302, 150 262, 230 256 S 330 182, 390 160 S 490 82, 560 46', 'end' => 46, 'y' => 13.5],
    ];
    $count = $projects->count() + 1;
@endphp

<section class="section showcase-section" id="selected-work">
    <div class="container-xl">
        <div class="section-head-row">
            <x-section-heading
                eyebrow="Selected work"
                title="Campaigns across <span class='text-gradient'>categories and continents</span>"
                lead="Client names are kept confidential. Projects are described by industry, market and platform." />
            <x-button :href="route('portfolio')" variant="ghost" icon="arrow-right" data-reveal>All {{ $totals['projects'] }} projects</x-button>
        </div>
    </div>

    <div class="showcase" data-showcase role="region" aria-roledescription="carousel" aria-label="Selected work">
        <div class="showcase__stage" data-showcase-stage tabindex="0" aria-label="Selected projects. Use the left and right arrow keys to browse.">
            <div class="showcase__track" data-showcase-track>
                @foreach ($projects as $project)
                    @php
                        $curve = $curves[$loop->index % count($curves)];
                        $markets = collect($project['markets']);
                    @endphp
                    <article class="showcase-slide" data-showcase-slide role="group" aria-roledescription="slide"
                        aria-label="{{ $loop->iteration }} of {{ $count }}: {{ $project['title'] }}">

                        {{-- Visual panel --}}
                        <div class="showcase-slide__visual showcase-slide__visual--{{ $project['category'] }}" aria-hidden="true">
                            <div class="showcase-slide__canvas" data-showcase-canvas>
                                <span class="showcase-slide__index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <svg class="showcase-slide__chart" viewBox="0 0 600 340" preserveAspectRatio="none">
                                    <g class="showcase-slide__grid">
                                        <line x1="0" y1="85" x2="600" y2="85" /><line x1="0" y1="170" x2="600" y2="170" /><line x1="0" y1="255" x2="600" y2="255" />
                                    </g>
                                    {{-- Shading continues flat past the end point so it reaches the panel edge. --}}
                                    <path class="showcase-slide__area" d="{{ $curve['d'] }} L600 {{ $curve['end'] }} V340 H0 Z" />
                                    <path class="showcase-slide__line" pathLength="1" d="{{ $curve['d'] }}" />
                                </svg>
                                {{-- The chart fills the lower 78% of the panel, so place the end dot within that band. --}}
                                <span class="showcase-slide__dot" style="top: {{ 22 + $curve['y'] * 0.78 }}%"></span>
                            </div>
                            <span class="showcase-slide__chip">{{ config('portfolio.categories.'.$project['category']) }}</span>
                            <span class="showcase-slide__illus">Illustrative</span>
                            <ul class="showcase-slide__platforms" role="list">
                                @foreach ($project['platforms'] as $platform)
                                    <li class="showcase-slide__platform showcase-slide__platform--{{ $platform }}"><x-icon :name="$platform" size="16" /></li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Information --}}
                        <div class="showcase-slide__info">
                            <p class="showcase-slide__meta">
                                <span>{{ config('portfolio.categories.'.$project['category']) }}</span>
                                <span>{{ $project['period'] }}</span>
                            </p>
                            <h3 class="showcase-slide__title">{{ $project['title'] }}</h3>
                            <p class="showcase-slide__where">
                                <x-icon name="map-pin" size="15" />
                                <span>{{ $markets->take(3)->implode(' · ') }}@if ($markets->count() > 3) · +{{ $markets->count() - 3 }} more @endif</span>
                                <span class="showcase-slide__sep" aria-hidden="true"></span>
                                <span>{{ $project['industry'] }}</span>
                            </p>
                            <p class="showcase-slide__text">{{ $project['objective'] }}</p>

                            @if ($project['outcome'])
                                <div class="showcase-slide__highlight showcase-slide__highlight--outcome">
                                    <span class="showcase-slide__highlight-label"><x-icon name="check" size="14" /> Documented outcome</span>
                                    <p>{{ $project['outcome'] }}</p>
                                </div>
                            @else
                                <div class="showcase-slide__highlight">
                                    <span class="showcase-slide__highlight-label"><x-icon name="layers" size="14" /> Work performed</span>
                                    <p>{{ collect($project['work'])->take(3)->implode(' · ') }}</p>
                                </div>
                            @endif

                            <div class="showcase-slide__foot">
                                <x-platform-badges :platforms="$project['platforms']" />
                                <a class="showcase-slide__link" href="{{ route('portfolio') }}#project-{{ $project['slug'] }}">
                                    View project <span class="showcase-slide__link-arrow"><x-icon name="arrow-right" size="16" /></span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach

                {{-- Closing slide: the full portfolio --}}
                <article class="showcase-slide showcase-slide--explore" data-showcase-slide role="group" aria-roledescription="slide"
                    aria-label="{{ $count }} of {{ $count }}: Explore every campaign">
                    <div class="showcase-slide__visual" aria-hidden="true">
                        <div class="showcase-slide__canvas" data-showcase-canvas>
                            <div class="showcase-stack">
                                @foreach (config('portfolio.categories') as $key => $label)
                                    <span class="mini-card mini-card--{{ $key }}">
                                        <span class="mini-card__label">{{ $label }}</span>
                                        <svg viewBox="0 0 120 50" preserveAspectRatio="none">
                                            <path class="mini-card__area" d="M0 42 C 18 40, 26 33, 40 32 S 64 24, 76 20 S 100 9, 120 5 V50 H0 Z" />
                                            <path class="mini-card__line" d="M0 42 C 18 40, 26 33, 40 32 S 64 24, 76 20 S 100 9, 120 5" />
                                        </svg>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <span class="showcase-slide__chip">Full portfolio</span>
                    </div>
                    <div class="showcase-slide__info">
                        <p class="showcase-slide__meta"><span>{{ $totals['projects'] }} projects</span><span>{{ $totals['markets'] }} markets</span></p>
                        <h3 class="showcase-slide__title">Explore every campaign</h3>
                        <p class="showcase-slide__text">E-commerce, lead generation and real estate work, filterable by platform, category and market.</p>
                        <div class="showcase-stats">
                            <span><strong>{{ $totals['projects'] }}</strong> projects</span>
                            <span><strong>{{ $totals['markets'] }}</strong> markets</span>
                            <span><strong>{{ $totals['platforms'] }}</strong> platforms</span>
                        </div>
                        <div class="showcase-slide__foot">
                            <x-button :href="route('portfolio')" icon="arrow-right">View all projects</x-button>
                        </div>
                    </div>
                </article>
            </div>
        </div>

        <div class="container-xl showcase__controls">
            <div class="showcase__pager">
                <p class="showcase__counter" aria-hidden="true"><strong data-showcase-current>01</strong><span>/ {{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</span></p>
                <div class="showcase__dots" role="group" aria-label="Choose a project">
                    @for ($i = 0; $i < $count; $i++)
                        <button type="button" class="showcase__dot" data-showcase-dot="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}" @if ($i === 0) aria-current="true" @endif>
                            <span class="showcase__dot-fill" data-showcase-fill></span>
                        </button>
                    @endfor
                </div>
            </div>
            <div class="showcase__buttons">
                <button type="button" class="showcase__btn showcase__btn--sm" data-showcase-toggle aria-label="Pause automatic slideshow" hidden>
                    <svg class="showcase__icon-pause" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                    <svg class="showcase__icon-play" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.5-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z"/></svg>
                </button>
                <button type="button" class="showcase__btn" data-showcase-prev aria-label="Previous project"><x-icon name="arrow-right" size="20" class="flip-x" /></button>
                <button type="button" class="showcase__btn" data-showcase-next aria-label="Next project"><x-icon name="arrow-right" size="20" /></button>
            </div>
        </div>

        <p class="visually-hidden" aria-live="polite" data-showcase-live></p>
    </div>
</section>
