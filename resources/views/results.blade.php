@extends('layouts.app')

@section('page', 'results')
@section('title', 'Results')
@section('description', 'Documented results from Ali Hassan\'s paid advertising work: approximately 4x average ROAS across an e-commerce portfolio, top campaigns at 8–10x, and portfolios of up to 16 brands.')

@php
    // Scope of each role, as documented in the CV.
    $scope = [
        ['role' => 'NextQuick Solutions', 'detail' => '5 e-commerce + 11 lead-gen brands', 'value' => 16, 'unit' => 'brands'],
        ['role' => 'Orbit Creator', 'detail' => 'Local client accounts at once', 'value' => 15, 'unit' => 'accounts'],
        ['role' => 'The Social Links', 'detail' => 'Brands managed simultaneously', 'value' => 10, 'unit' => 'brands'],
        ['role' => 'Zela Boost', 'detail' => '3 e-commerce + 5 lead-gen brands', 'value' => 8, 'unit' => 'brands'],
    ];
    $scopeMax = 16;
@endphp

@section('content')

<x-page-hero
    variant="results"
    eyebrow="Results"
    title="Results, with the <span class='text-gradient'>context</span> they were earned in."
    lead="Every figure below comes from documented work history, alongside where and when it was achieved. Past performance doesn't guarantee future results.">
    <x-slot:visual>
        <div class="hero-graph" data-hero-graph>
            <svg viewBox="0 0 600 300" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="hero-graph-fill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#4F7CFF" stop-opacity="0.35" />
                        <stop offset="1" stop-color="#4F7CFF" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <g class="hero-graph__grid">
                    <line x1="0" y1="75" x2="600" y2="75" /><line x1="0" y1="150" x2="600" y2="150" /><line x1="0" y1="225" x2="600" y2="225" />
                </g>
                <path class="hero-graph__area" d="M0 260 C 80 250, 120 220, 190 210 S 300 160, 360 140 S 480 70, 600 40 V300 H0 Z" fill="url(#hero-graph-fill)" />
                <path class="hero-graph__line" data-graph-line d="M0 260 C 80 250, 120 220, 190 210 S 300 160, 360 140 S 480 70, 600 40" />
            </svg>
            {{-- Pins sit at fixed points on the curve (as % of the viewBox). Illustrative, not a data plot. --}}
            <span class="hero-graph__dot" data-graph-dot></span>
            <span class="hero-graph__pin" style="--x: 60%; --y: 46.7%" data-graph-pin>~4x avg</span>
            <span class="hero-graph__pin hero-graph__pin--peak" style="--x: 100%; --y: 13.3%" data-graph-pin>8–10x peak</span>
        </div>
    </x-slot:visual>
</x-page-hero>

<section class="section section--flush-top">
    <div class="container-xl">
        <div class="stats-grid">
            @foreach ($stats as $stat)
                <x-stat :stat="$stat" />
            @endforeach
        </div>
    </div>
</section>

{{-- ============ BEFORE & AFTER ============ --}}
@include('partials.case-studies-section', [
    'caseStudies' => $caseStudies,
    'full' => true,
    'title' => "See the difference: <span class='text-gradient'>before &amp; after</span>",
    'lead' => 'Each case study compares a brand’s advertising performance before Ali’s involvement with the results after it. Drag the slider, switch to side-by-side, or open any screenshot full size.',
])

{{-- ============ ROAS ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <x-section-heading eyebrow="Return on ad spend" title="Around 4x on average. Up to 10x at the top."
                    lead="Measured across the e-commerce portfolio at The Social Links (Dec 2024 – Jun 2026) — fashion, home textiles, jewellery and food brands selling into Australia, Canada, the UK and the USA." />
            </div>
            <div class="col-lg-7">
                <figure class="roas-chart" data-roas data-custom-anim>
                    <figcaption class="chart-title">ROAS, The Social Links e-commerce portfolio</figcaption>
                    <div class="roas-chart__plot">
                        <div class="roas-chart__track">
                            <span class="roas-chart__band" style="--from: 80%; --to: 100%" data-tip="Top-performing campaigns: 8–10x ROAS" tabindex="0"></span>
                            <span class="roas-chart__bar" style="--to: 40%" data-roas-bar data-tip="Portfolio average: approximately 4x ROAS" tabindex="0"></span>
                            <span class="roas-chart__ref" style="--at: 10%"></span>
                        </div>
                        <div class="roas-chart__labels">
                            <span class="roas-chart__label" style="--at: 10%">1x<small>break-even</small></span>
                            <span class="roas-chart__label roas-chart__label--strong" style="--at: 40%">~4x<small>average</small></span>
                            <span class="roas-chart__label roas-chart__label--strong" style="--at: 90%">8–10x<small>top campaigns</small></span>
                        </div>
                        <div class="roas-chart__axis" aria-hidden="true">
                            @foreach ([0, 2, 4, 6, 8, 10] as $tick)
                                <span style="--at: {{ $tick * 10 }}%">{{ $tick }}x</span>
                            @endforeach
                        </div>
                    </div>
                    <table class="visually-hidden">
                        <caption>Return on ad spend, The Social Links e-commerce portfolio, Dec 2024 – Jun 2026</caption>
                        <tr><th scope="row">Portfolio average</th><td>Approximately 4x</td></tr>
                        <tr><th scope="row">Top-performing campaigns</th><td>8–10x</td></tr>
                    </table>
                    <p class="chart-note">ROAS = revenue attributed to ads ÷ ad spend. A 4x ROAS means roughly four units of revenue for every unit spent.</p>
                </figure>
            </div>
        </div>
    </div>
</section>

{{-- ============ SCALE ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-lg-7 order-lg-2">
                <figure class="bar-chart" data-bars data-custom-anim>
                    <figcaption class="chart-title">Portfolio size by role</figcaption>
                    <p class="chart-subtitle">Brands or client accounts managed at the same time</p>
                    <ul class="bar-chart__rows" role="list">
                        @foreach ($scope as $row)
                            <li class="bar-chart__row" tabindex="0" data-tip="{{ $row['role'] }}: {{ $row['value'] }} {{ $row['unit'] }} — {{ $row['detail'] }}">
                                <span class="bar-chart__name">{{ $row['role'] }}<small>{{ $row['detail'] }}</small></span>
                                <span class="bar-chart__track"><span class="bar-chart__bar" style="--w: {{ round($row['value'] / $scopeMax * 100) }}%" data-bar></span></span>
                                <span class="bar-chart__value"><span data-count="{{ $row['value'] }}">{{ $row['value'] }}</span> <small>{{ $row['unit'] }}</small></span>
                            </li>
                        @endforeach
                    </ul>
                </figure>
            </div>
            <div class="col-lg-5 order-lg-1">
                <x-section-heading eyebrow="Scale" title="Trusted with bigger portfolios, year after year" />
                <div class="compare-cards" data-compare-cards data-custom-anim>
                    <div class="compare-card" data-reveal-card>
                        <p class="compare-card__label">Portfolio growth · The Social Links</p>
                        <div class="compare-card__row">
                            <span class="compare-card__from">2–3<small>accounts</small></span>
                            <x-icon name="arrow-right" size="22" />
                            <span class="compare-card__to"><span data-count="10">10</span><small>brands at once</small></span>
                        </div>
                    </div>
                    <div class="compare-card" data-reveal-card>
                        <p class="compare-card__label">Daily Meta ad spend managed</p>
                        <p class="compare-card__big">PKR 60,000–80,000</p>
                        <p class="compare-card__note">Approximate, per day, across the portfolio</p>
                    </div>
                    <div class="compare-card" data-reveal-card>
                        <p class="compare-card__label">Teams led</p>
                        <div class="compare-card__row">
                            <span class="compare-card__to"><span data-count="7">7</span><small>PPC specialists</small></span>
                            <span class="compare-card__to">10+<small>marketing team</small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ HOW RESULTS ARE DRIVEN ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading eyebrow="What drives the numbers" title="The optimisation work behind the results" align="center" />
        <div class="services-grid">
            @foreach ([
                ['pulse', 'Reliable tracking', 'Meta Pixel, GTM and GA4 conversion tracking set up so results are measured on real conversion events.'],
                ['layers', 'Creative testing', 'A/B tests on hooks, angles and formats; weaker assets revised with designers and video editors.'],
                ['target', 'Audience & keyword refinement', 'Segments, keywords and bids adjusted from campaign and lead-quality data.'],
                ['gauge', 'Search efficiency', 'Quality Score and click-through-rate improvements on Google Search campaigns.'],
                ['chart', 'Budget allocation', 'Budgets moved between campaigns, audiences and brands towards what performs.'],
                ['funnel', 'Lead-quality review', 'Lead quality, conversion volume and acquisition costs reviewed — not just lead counts.'],
            ] as [$icon, $title, $text])
                <x-service-card :service="['icon' => $icon, 'title' => $title, 'text' => $text]" />
            @endforeach
        </div>
    </div>
</section>

{{-- ============ MARKETS ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading eyebrow="By market" title="Where the work was done" />
        <div class="table-wrap" data-market-table data-custom-anim>
            <table class="data-table">
                <thead>
                    <tr><th scope="col">Market</th><th scope="col">Focus</th><th scope="col">Work performed</th></tr>
                </thead>
                <tbody>
                    @foreach ($markets as $market)
                        <tr>
                            <th scope="row"><span class="market-list__code">{{ $market['code'] }}</span> {{ $market['name'] }}</th>
                            <td>{{ $market['focus'] }}</td>
                            <td>{{ $market['work'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="fine-print" data-reveal>Cost per lead, cost per acquisition and conversion volume are monitored across accounts. Client-level figures are not published on this site.</p>
    </div>
</section>

<x-cta title="Let's set targets for your account." />

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/results.js') }}?v={{ filemtime(public_path('assets/js/pages/results.js')) }}" defer></script>
@endpush
