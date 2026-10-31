@extends('layouts.app')

@php
    $c = config('content.home');
@endphp

@section('page', 'home')
@section('title', txt($c['seo_title'] ?? ''))
@section('description', txt($c['seo_description']))

@section('content')

{{-- ============ HERO ============ --}}
<section class="hero" data-page-hero>
    <div class="hero__canvas" data-hero-scene aria-hidden="true">
        <div class="hero__fallback">
            <div class="hero__fallback-bars"><span></span><span></span><span></span><span></span><span></span><span></span></div>
        </div>
    </div>
    <div class="hero__vignette" aria-hidden="true"></div>

    <div class="container-xl hero__inner">
        <div class="hero__copy">
            <p class="eyebrow" data-hero-item><span class="pulse-dot" aria-hidden="true"></span> {{ $person['role'] }} · {{ $person['location'] }}</p>
            <h1 class="hero__title" data-split="hero">{{ rich($c['hero']['title']) }}</h1>
            <p class="hero__lead" data-hero-item>{{ txt($person['intro']) }}</p>
            <div class="hero__actions" data-hero-item>
                <x-button :href="route('portfolio')" icon="arrow-right">{{ txt($c['hero']['primary']) }}</x-button>
                <x-button :href="route('contact')" variant="ghost">{{ txt($c['hero']['secondary']) }}</x-button>
            </div>
        </div>

        <ul class="hero__chips" role="list" aria-label="Highlights">
            @foreach ($c['hero']['chips'] as $chip)
                <li class="glass-chip glass-chip--{{ ['a', 'b'][$loop->index] ?? 'b' }}" data-hero-chip>
                    <span class="glass-chip__value">{{ txt($chip['value']) }}</span>
                    <span class="glass-chip__label">{{ rich($chip['label']) }}</span>
                </li>
            @endforeach
            <li class="glass-chip glass-chip--c" data-hero-chip>
                <span class="glass-chip__icons"><x-icon name="meta" size="18" /><x-icon name="google" size="18" /><x-icon name="tiktok" size="18" /></span>
                <span class="glass-chip__label">{{ txt($c['hero']['platforms_chip']) }}</span>
            </li>
        </ul>
    </div>

    <div class="hero__markets container-xl" data-hero-item>
        <span class="hero__markets-label">{{ txt($c['hero']['markets_label']) }}</span>
        <ul role="list">
            @foreach ($c['hero']['markets'] as $market)
                <li>{{ $market }}</li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ============ STATS ============ --}}
<section class="section section--tight" aria-label="Key figures">
    <div class="container-xl">
        <div class="stats-grid">
            @foreach ($stats as $stat)
                <x-stat :stat="$stat" />
            @endforeach
        </div>
    </div>
</section>

{{-- ============ PLATFORM TABS ============ --}}
<section class="section" id="platforms">
    <div class="container-xl">
        <x-section-heading
            :eyebrow="txt($c['platforms']['eyebrow'])"
            :title="rich($c['platforms']['title'])"
            :lead="txt($c['platforms']['lead'])" />

        <div class="platform-tabs" data-tabs>
            <div class="platform-tabs__list" role="tablist" aria-label="Advertising platforms">
                <span class="platform-tabs__indicator" data-tab-indicator aria-hidden="true"></span>
                @foreach ($platforms as $key => $platform)
                    <button type="button" class="platform-tabs__tab" role="tab"
                        id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}"
                        data-tab="{{ $key }}">
                        <x-icon :name="$key" size="20" />
                        {{ $platform['name'] }}
                    </button>
                @endforeach
            </div>

            @foreach ($platforms as $key => $platform)
                <div class="platform-panel platform-panel--{{ $key }}" role="tabpanel" id="panel-{{ $key }}" aria-labelledby="tab-{{ $key }}" tabindex="0" data-panel="{{ $key }}" @unless ($loop->first) hidden @endunless>
                    <div class="row g-4 g-xl-5 align-items-stretch">
                        <div class="col-lg-5">
                            <div class="platform-panel__intro" data-panel-item>
                                <p class="platform-panel__tagline">{{ $platform['tagline'] }}</p>
                                <p class="platform-panel__summary">{{ $platform['summary'] }}</p>

                                @include('partials.platform-visual', ['key' => $key])

                                <div class="platform-panel__metrics">
                                    @foreach ($platform['metrics'] as $metric)
                                        <div>
                                            <p class="platform-panel__metric-value">{{ $metric['value'] }}</p>
                                            <p class="platform-panel__metric-label">{{ $metric['label'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <ul class="platform-services" role="list">
                                @foreach ($platform['services'] as $service)
                                    <li class="platform-service" data-panel-item>
                                        <span class="icon-chip icon-chip--sm"><x-icon :name="$service['icon']" size="20" /></span>
                                        <div>
                                            <h3 class="platform-service__title">{{ $service['title'] }}</h3>
                                            <p class="platform-service__text">{{ $service['text'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="also-runs" data-reveal>
            {{ txt($c['platforms']['also_label']) }} @foreach ($alsoRuns as $item)<span>{{ $item }}</span>@endforeach
        </p>
    </div>
</section>

{{-- ============ INTRO ============ --}}
<section class="section intro-section">
    <div class="container-xl">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div class="portrait-card" data-reveal-card>
                    @if (! empty($person['photo']))
                        <img class="portrait-card__photo" src="{{ media_url($person['photo']) }}" alt="{{ $person['name'] }}" loading="lazy" decoding="async">
                    @else
                        <div class="portrait-card__monogram" aria-hidden="true">{{ config('content.site.monogram') }}</div>
                    @endif
                    <div class="portrait-card__meta">
                        <p class="portrait-card__name">{{ $person['name'] }}</p>
                        <p class="portrait-card__role">{{ $person['role'] }}</p>
                    </div>
                    <ul class="portrait-card__facts" role="list">
                        @foreach ($c['intro']['facts'] as $fact)
                            <li><x-icon :name="$fact['icon']" size="18" /> {{ txt($fact['text']) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <x-section-heading :eyebrow="txt($c['intro']['eyebrow'])" :title="rich($c['intro']['title'])" />
                <div class="prose" data-reveal>
                    @foreach (array_slice($person['summary'], 0, 2) as $paragraph)
                        <p>{{ txt($paragraph) }}</p>
                    @endforeach
                </div>
                <div data-reveal>
                    <x-button :href="route('about')" variant="ghost" icon="arrow-right">{{ txt($c['intro']['button']) }}</x-button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ SERVICES ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="section-head-row">
            <x-section-heading :eyebrow="txt($c['services']['eyebrow'])" :title="rich($c['services']['title'])" />
            <x-button :href="route('services')" variant="ghost" icon="arrow-right" data-reveal>{{ txt($c['services']['button']) }}</x-button>
        </div>
        <div class="services-grid">
            @foreach ($services as $service)
                <x-service-card :service="$service" :index="$loop->iteration" />
            @endforeach
        </div>
    </div>
</section>

{{-- ============ SKILLS ============ --}}
<section class="section section--band">
    <div class="marquee" aria-hidden="true">
        <div class="marquee__track" data-marquee>
            @for ($i = 0; $i < 2; $i++)
                @foreach ($c['skills']['marquee'] as $item)
                    <span>{{ $item }}</span>
                @endforeach
            @endfor
        </div>
    </div>
    <div class="container-xl" id="expertise">
        <x-section-heading :eyebrow="txt($c['skills']['eyebrow'])" :title="rich($c['skills']['title'])" align="center" />
        @include('partials.skill-groups', ['skills' => $skills, 'columns' => 3])
    </div>
</section>

{{-- ============ PROJECTS ============ --}}
<section class="section" id="selected-work">
    <div class="container-xl">
        <div class="section-head-row">
            <x-section-heading :eyebrow="txt($c['work']['eyebrow'])" :title="rich($c['work']['title'])" :lead="txt($c['work']['lead'])" />
            <div class="slider-controls" data-reveal>
                <button type="button" class="round-btn" data-slider-prev aria-label="Previous project"><x-icon name="arrow-right" size="18" class="flip-x" /></button>
                <button type="button" class="round-btn" data-slider-next aria-label="Next project"><x-icon name="arrow-right" size="18" /></button>
            </div>
        </div>
    </div>
    <div class="project-slider" data-slider tabindex="0" aria-label="Selected projects">
        @foreach ($featuredProjects as $project)
            <x-project-card :project="$project" class="project-slider__item" />
        @endforeach
        @include('partials.explore-card', ['totals' => $portfolioTotals])
    </div>
</section>

{{-- ============ BEFORE & AFTER ============ --}}
@include('partials.case-studies-section', [
    'caseStudies' => $caseStudies,
    'title' => $c['case_studies']['title'],
    'lead' => $c['case_studies']['lead'],
])

{{-- ============ INTERNATIONAL ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="intl-teaser" data-reveal-card>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <x-section-heading :eyebrow="txt($c['international']['eyebrow'])" :title="rich($c['international']['title'])" :lead="txt($c['international']['lead'])" />
                    <div data-reveal>
                        <x-button :href="route('international')" icon="globe">{{ txt($c['international']['button']) }}</x-button>
                    </div>
                    {{-- Market globe (globe.js, loaded when scrolled near); desktop only. --}}
                    <div class="globe globe--teaser" data-globe data-lazy data-markets="{{ $globeMarkets->toJson() }}" aria-hidden="true">
                        <div class="globe__fallback"><x-icon name="globe" size="90" /></div>
                        <div class="globe__labels" data-globe-labels></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <ul class="market-list" role="list">
                        @foreach ($markets as $market)
                            <li class="market-list__item" data-reveal>
                                <span class="market-list__code">{{ $market['code'] }}</span>
                                <span class="market-list__name">{{ $market['name'] }}</span>
                                <span class="market-list__focus">{{ $market['focus'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ ACHIEVEMENTS & QUALIFICATIONS ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-lg-7">
                <x-section-heading :eyebrow="txt($c['achievements']['eyebrow'])" :title="rich($c['achievements']['title'])" />
                <ol class="milestones" role="list">
                    @foreach ($c['achievements']['items'] as $item)
                        <li class="milestone" data-reveal>
                            <span class="milestone__headline">{{ txt($item['headline']) }}</span>
                            <span class="milestone__detail">{{ txt($item['detail']) }}</span>
                        </li>
                    @endforeach
                </ol>
                <div data-reveal>
                    <x-button :href="route('results')" variant="ghost" icon="arrow-right">{{ txt($c['achievements']['button']) }}</x-button>
                </div>
            </div>
            <div class="col-lg-5">
                <x-section-heading :eyebrow="txt($c['education']['eyebrow'])" :title="rich($c['education']['title'])" />
                @include('partials.education-list', ['education' => $education, 'certifications' => $certifications])
            </div>
        </div>
    </div>
</section>

<x-cta :title="$c['cta']['title']" :text="$c['cta']['text']" />

@endsection

@section('three')
    <script type="module" src="{{ asset('assets/js/three/hero-scene.js') }}?v={{ filemtime(public_path('assets/js/three/hero-scene.js')) }}"></script>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/home.js') }}?v={{ filemtime(public_path('assets/js/pages/home.js')) }}" defer></script>
@endpush
