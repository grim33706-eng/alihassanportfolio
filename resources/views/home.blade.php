@extends('layouts.app')

@section('page', 'home')
@section('description', 'Ali Hassan is a Performance Marketing Manager in Lahore with 4+ years running Meta Ads, Google Ads and TikTok Ads for e-commerce sales and B2B/B2C lead generation in the USA, Canada, UK and Australia.')

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
            <h1 class="hero__title" data-split="hero">Paid media that turns ad spend into <span class="text-gradient">revenue</span> and qualified&nbsp;leads.</h1>
            <p class="hero__lead" data-hero-item>{{ $person['intro'] }}</p>
            <div class="hero__actions" data-hero-item>
                <x-button :href="route('portfolio')" icon="arrow-right">View my work</x-button>
                <x-button :href="route('contact')" variant="ghost">Contact me</x-button>
            </div>
        </div>

        <ul class="hero__chips" role="list" aria-label="Highlights">
            <li class="glass-chip glass-chip--a" data-hero-chip>
                <span class="glass-chip__value">~4x</span>
                <span class="glass-chip__label">Average ROAS<br>e-commerce portfolio</span>
            </li>
            <li class="glass-chip glass-chip--b" data-hero-chip>
                <span class="glass-chip__value">8–10x</span>
                <span class="glass-chip__label">Top-performing<br>campaigns</span>
            </li>
            <li class="glass-chip glass-chip--c" data-hero-chip>
                <span class="glass-chip__icons"><x-icon name="meta" size="18" /><x-icon name="google" size="18" /><x-icon name="tiktok" size="18" /></span>
                <span class="glass-chip__label">Meta · Google · TikTok</span>
            </li>
        </ul>
    </div>

    <div class="hero__markets container-xl" data-hero-item>
        <span class="hero__markets-label">Campaigns run for</span>
        <ul role="list">
            @foreach (['USA', 'Canada', 'UK', 'France', 'Australia', 'UAE', 'Saudi Arabia', 'Oman', 'Azerbaijan', 'Uzbekistan', 'Bangladesh', 'Pakistan'] as $market)
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
            eyebrow="Core platforms"
            title="Two platforms. One goal: <span class='text-gradient'>profitable growth.</span>"
            lead="Meta Ads builds demand and drives sales; Google Ads captures the intent that already exists. Here is what I run on each." />

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
            Also running: @foreach ($alsoRuns as $item)<span>{{ $item }}</span>@endforeach
        </p>
    </div>
</section>

{{-- ============ INTRO ============ --}}
<section class="section intro-section">
    <div class="container-xl">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <div class="portrait-card" data-reveal-card>
                    <div class="portrait-card__monogram" aria-hidden="true">AH</div>
                    <div class="portrait-card__meta">
                        <p class="portrait-card__name">{{ $person['name'] }}</p>
                        <p class="portrait-card__role">{{ $person['role'] }}</p>
                    </div>
                    <ul class="portrait-card__facts" role="list">
                        <li><x-icon name="users" size="18" /> Led a 7-person PPC team</li>
                        <li><x-icon name="layers" size="18" /> Up to 16 brands in one portfolio</li>
                        <li><x-icon name="globe" size="18" /> 12 markets across 4 continents</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <x-section-heading eyebrow="Introduction" title="Hi, I'm Ali. I run ads that have to <span class='text-gradient'>pay for themselves.</span>" />
                <div class="prose" data-reveal>
                    @foreach (array_slice($person['summary'], 0, 2) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
                <div data-reveal>
                    <x-button :href="route('about')" variant="ghost" icon="arrow-right">More about me</x-button>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ SERVICES ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="section-head-row">
            <x-section-heading eyebrow="Services" title="What I can take off your plate" />
            <x-button :href="route('services')" variant="ghost" icon="arrow-right" data-reveal>All services</x-button>
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
                @foreach (['Meta Ads', 'Google Search', 'Performance Max', 'Remarketing', 'TikTok Ads', 'GA4', 'Google Tag Manager', 'Meta Pixel', 'A/B Testing', 'Lead Generation', 'E-commerce', 'Bidding Strategy'] as $item)
                    <span>{{ $item }}</span>
                @endforeach
            @endfor
        </div>
    </div>
    <div class="container-xl" id="expertise">
        <x-section-heading eyebrow="Expertise" title="The toolkit behind the results" align="center" />
        @include('partials.skill-groups', ['skills' => $skills, 'columns' => 3])
    </div>
</section>

{{-- ============ PROJECTS ============ --}}
@include('partials.showcase', ['projects' => $featuredProjects, 'totals' => $portfolioTotals])

{{-- ============ BEFORE & AFTER ============ --}}
@include('partials.case-studies-section', [
    'caseStudies' => $caseStudies,
    'title' => "Before Ali. <span class='text-gradient'>After Ali.</span>",
    'lead' => 'Selected case studies comparing campaign performance before and after Ali took over.',
])

{{-- ============ INTERNATIONAL ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="intl-teaser" data-reveal-card>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <x-section-heading eyebrow="International experience" title="Campaigns built for <span class='text-gradient'>twelve markets</span>" lead="From e-commerce sales in the UK, USA, Canada and Australia and service-business leads in France, to real estate leads in Dubai, Azerbaijan and Uzbekistan and Meta campaigns across the Gulf and South Asia." />
                    <div data-reveal>
                        <x-button :href="route('international')" icon="globe">Explore the markets</x-button>
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
                <x-section-heading eyebrow="Achievements" title="Documented milestones" />
                <ol class="milestones" role="list">
                    @foreach ([
                        ['~4x average ROAS', 'Across an e-commerce portfolio of up to 10 brands, with top campaigns reaching 8–10x — The Social Links.'],
                        ['2–3 → 10 brands', 'Scaled the managed portfolio while handling approximately PKR 60,000–80,000 in daily Meta ad spend.'],
                        ['Team of 10+ led', 'Paid media specialists, designers and video creators across 5 e-commerce and 11 lead-generation brands — NextQuick Solutions.'],
                        ['7-person PPC team', 'Leading PPC and Google Ads specialists on lead generation for USA and Canada clients — Dynasty Marketing Agency.'],
                        ['15 accounts at once', 'Managed local lead-generation clients end to end — Orbit Creator.'],
                    ] as [$headline, $detail])
                        <li class="milestone" data-reveal>
                            <span class="milestone__headline">{{ $headline }}</span>
                            <span class="milestone__detail">{{ $detail }}</span>
                        </li>
                    @endforeach
                </ol>
                <div data-reveal>
                    <x-button :href="route('results')" variant="ghost" icon="arrow-right">See results in context</x-button>
                </div>
            </div>
            <div class="col-lg-5">
                <x-section-heading eyebrow="Qualifications" title="Education" />
                <div class="edu-list">
                    @foreach ($education as $item)
                        <div class="edu-card" data-reveal-card>
                            <span class="icon-chip icon-chip--sm"><x-icon name="graduation" size="20" /></span>
                            <div>
                                <p class="edu-card__title">{{ $item['title'] }}</p>
                                <p class="edu-card__school">{{ $item['school'] }}</p>
                                <p class="edu-card__period">{{ $item['period'] }}</p>
                            </div>
                        </div>
                    @endforeach
                    @foreach ($certifications as $cert)
                        <div class="edu-card" data-reveal-card>
                            <span class="icon-chip icon-chip--sm"><x-icon name="check" size="20" /></span>
                            <div>
                                <p class="edu-card__title">{{ $cert['title'] }}</p>
                                <p class="edu-card__school">{{ $cert['issuer'] }}</p>
                                <p class="edu-card__period">{{ $cert['year'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<x-cta />

@endsection

@section('three')
    <script type="module" src="{{ asset('assets/js/three/hero-scene.js') }}?v={{ filemtime(public_path('assets/js/three/hero-scene.js')) }}"></script>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/home.js') }}?v={{ filemtime(public_path('assets/js/pages/home.js')) }}" defer></script>
@endpush
