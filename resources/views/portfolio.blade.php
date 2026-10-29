@extends('layouts.app')

@php
    $c = config('content.portfolio');
    // Real project facts for the 3D hero cards (industry, platforms, category) — no figures.
    $sceneCards = collect($projects)->map(fn ($p) => [
        'industry' => $p['industry'],
        'category' => config('portfolio.categories.'.$p['category'], ''),
        'platforms' => array_values($p['platforms']),
    ])->values();
@endphp

@section('page', 'portfolio')
@section('title', txt($c['seo_title']))
@section('description', txt($c['seo_description']))

@section('content')

<x-page-hero
    variant="portfolio"
    :eyebrow="txt($c['hero']['eyebrow'])"
    :title="rich($c['hero']['title'])"
    :lead="txt($c['hero']['lead'])"
    :image="$c['hero']['image'] ?? null">
    <x-slot:visual>
        {{-- 3D campaign carousel (portfolio-scene.js); the cards stay as the fallback without WebGL. --}}
        <div class="services-scene portfolio-scene" data-portfolio-scene data-cards="{{ $sceneCards->toJson() }}">
            <div class="hero-cards">
                <span></span><span></span><span></span>
            </div>
        </div>
    </x-slot:visual>
</x-page-hero>

<section class="section section--flush-top">
    <div class="container-xl">
        @php
            $total = $projects->count();
            $platformCount = fn ($key) => $projects->filter(fn ($p) => in_array($key, $p['platforms'], true))->count();
            $categoryCount = fn ($key) => $projects->where('category', $key)->count();
        @endphp

        <div class="pf-filters" data-filters>
            <span class="pf-filters__orb" aria-hidden="true"></span>

            {{-- Left: title, live results meter, active-filter summary, reset --}}
            <div class="pf-filters__aside">
                <div class="pf-filters__heading">
                    <span class="pf-filters__icon"><x-icon name="funnel" size="20" /></span>
                    <div>
                        <p class="pf-filters__title">{{ txt($c['filters']['title']) }}</p>
                        <p class="pf-filters__sub">{{ txt($c['filters']['subtitle']) }}</p>
                    </div>
                </div>

                <div class="pf-meter">
                    <svg class="pf-meter__ring" viewBox="0 0 72 72" aria-hidden="true">
                        <defs>
                            <linearGradient id="pf-meter-grad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#6d93ff" />
                                <stop offset="1" stop-color="#8B5CF6" />
                            </linearGradient>
                        </defs>
                        <circle class="pf-meter__track" cx="36" cy="36" r="30" />
                        <circle class="pf-meter__fill" cx="36" cy="36" r="30" pathLength="100" data-filter-ring />
                    </svg>
                    <span class="pf-meter__value"><strong data-filter-visible>{{ $total }}</strong><small>/{{ $total }}</small></span>
                    <p class="pf-meter__text" aria-live="polite" data-filter-count>
                        <span class="pf-meter__label">{{ txt($c['filters']['showing']) }}</span>
                        <span class="pf-meter__summary" data-filter-summary data-default="{{ txt($c['filters']['all_projects']) }}">{{ txt($c['filters']['all_projects']) }}</span>
                    </p>
                </div>

                <button type="button" class="pf-filters__reset" data-filter-reset disabled>
                    <x-icon name="repeat" size="15" /> {{ txt($c['filters']['reset']) }}
                </button>
            </div>

            {{-- Right: one row per filter --}}
            <div class="pf-filters__groups">
                <div class="pf-row" role="group" aria-label="Filter by platform">
                    <span class="pf-row__label"><x-icon name="layers" size="16" /> Platform</span>
                    <div class="pf-track">
                        <button type="button" class="pf-chip is-active" data-filter="platform" data-value="all" data-label="All platforms" aria-pressed="true">
                            All <span class="pf-chip__count">{{ $total }}</span>
                        </button>
                        @foreach ($platformFilters as $key => $label)
                            <button type="button" class="pf-chip pf-chip--{{ $key }}" data-filter="platform" data-value="{{ $key }}" data-label="{{ $label }}" aria-pressed="false">
                                <x-icon :name="$key" size="15" /> {{ $label }} <span class="pf-chip__count">{{ $platformCount($key) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="pf-row" role="group" aria-label="Filter by category">
                    <span class="pf-row__label"><x-icon name="tag" size="16" /> Category</span>
                    <div class="pf-track">
                        <button type="button" class="pf-chip is-active" data-filter="category" data-value="all" data-label="All categories" aria-pressed="true">
                            All <span class="pf-chip__count">{{ $total }}</span>
                        </button>
                        @foreach ($categories as $key => $label)
                            <button type="button" class="pf-chip" data-filter="category" data-value="{{ $key }}" data-label="{{ $label }}" aria-pressed="false">
                                {{ $label }} <span class="pf-chip__count">{{ $categoryCount($key) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="pf-row">
                    <label class="pf-row__label" for="market-filter"><x-icon name="globe" size="16" /> Market</label>
                    <div class="pf-select-wrap">
                        <x-icon name="map-pin" size="16" />
                        <select id="market-filter" class="pf-select" data-filter-select="market">
                            <option value="all">All markets</option>
                            @foreach ($projects->pluck('markets')->flatten()->unique()->sort() as $market)
                                <option value="{{ \Illuminate\Support\Str::slug($market) }}">{{ $market }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="project-grid" data-project-grid>
            @foreach ($projects as $project)
                <x-project-card :project="$project" />
            @endforeach
        </div>

        <div class="empty-state" data-empty-state hidden>
            <x-icon name="search" size="28" />
            <p>{{ txt($c['filters']['empty']) }}</p>
            <button type="button" class="btn-ui btn-ui--ghost btn-ui--sm" data-filter-reset><span class="btn-ui__label">{{ txt($c['filters']['reset']) }}</span></button>
        </div>
    </div>
</section>

@include('partials.case-studies-section', [
    'caseStudies' => $caseStudies,
    'title' => $c['case_studies']['title'],
    'lead' => $c['case_studies']['lead'],
])

<x-cta :title="$c['cta']['title']" :text="$c['cta']['text']" />

@endsection

@section('three')
    @if (empty($c['hero']['image']))
    <script type="module" src="{{ asset('assets/js/three/portfolio-scene.js') }}?v={{ filemtime(public_path('assets/js/three/portfolio-scene.js')) }}"></script>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/Flip.min.js" defer></script>
    <script src="{{ asset('assets/js/pages/portfolio.js') }}?v={{ filemtime(public_path('assets/js/pages/portfolio.js')) }}" defer></script>
@endpush
