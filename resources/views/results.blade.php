@extends('layouts.app')

@php
    $c = config('content.results');
    $roas = $c['roas'];
    $scale = $c['scale'];
    // ROAS chart runs 0–10x: each 1x is 10% of the track.
    $pct = fn ($x) => max(0, min(100, (float) $x * 10));
    $scopeMax = max(1, collect($scale['bars'])->max('value'));
    $countable = fn ($v) => is_numeric($v);
@endphp

@section('page', 'results')
@section('title', txt($c['seo_title']))
@section('description', txt($c['seo_description']))

@section('content')

<x-page-hero
    variant="results"
    :eyebrow="txt($c['hero']['eyebrow'])"
    :title="rich($c['hero']['title'])"
    :lead="txt($c['hero']['lead'])"
    :image="$c['hero']['image'] ?? null">
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
            <span class="hero-graph__pin" style="--x: 60%; --y: 46.7%" data-graph-pin>{{ txt($c['hero']['pin_average']) }}</span>
            <span class="hero-graph__pin hero-graph__pin--peak" style="--x: 100%; --y: 13.3%" data-graph-pin>{{ txt($c['hero']['pin_peak']) }}</span>
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
    'title' => $c['case_studies']['title'],
    'lead' => $c['case_studies']['lead'],
])

{{-- ============ ROAS ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <x-section-heading :eyebrow="txt($roas['eyebrow'])" :title="rich($roas['title'])" :lead="txt($roas['lead'])" />
            </div>
            <div class="col-lg-7">
                <figure class="roas-chart" data-roas data-custom-anim>
                    <figcaption class="chart-title">{{ txt($roas['chart_title']) }}</figcaption>
                    <div class="roas-chart__plot">
                        <div class="roas-chart__track">
                            <span class="roas-chart__band" style="--from: {{ $pct($roas['peak_from']) }}%; --to: {{ $pct($roas['peak_to']) }}%" data-tip="{{ txt($roas['peak_tip']) }}" tabindex="0"></span>
                            <span class="roas-chart__bar" style="--to: {{ $pct($roas['average']) }}%" data-roas-bar data-tip="{{ txt($roas['average_tip']) }}" tabindex="0"></span>
                            <span class="roas-chart__ref" style="--at: 10%"></span>
                        </div>
                        <div class="roas-chart__labels">
                            <span class="roas-chart__label" style="--at: 10%">1x<small>{{ txt($roas['breakeven_caption']) }}</small></span>
                            <span class="roas-chart__label roas-chart__label--strong" style="--at: {{ $pct($roas['average']) }}%">{{ txt($roas['average_label']) }}<small>{{ txt($roas['average_caption']) }}</small></span>
                            <span class="roas-chart__label roas-chart__label--strong" style="--at: {{ ($pct($roas['peak_from']) + $pct($roas['peak_to'])) / 2 }}%">{{ txt($roas['peak_label']) }}<small>{{ txt($roas['peak_caption']) }}</small></span>
                        </div>
                        <div class="roas-chart__axis" aria-hidden="true">
                            @foreach ([0, 2, 4, 6, 8, 10] as $tick)
                                <span style="--at: {{ $tick * 10 }}%">{{ $tick }}x</span>
                            @endforeach
                        </div>
                    </div>
                    <table class="visually-hidden">
                        <caption>{{ txt($roas['table_caption']) }}</caption>
                        <tr><th scope="row">Portfolio average</th><td>{{ txt($roas['table_average']) }}</td></tr>
                        <tr><th scope="row">Top-performing campaigns</th><td>{{ txt($roas['peak_label']) }}</td></tr>
                    </table>
                    <p class="chart-note">{{ txt($roas['note']) }}</p>
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
                    <figcaption class="chart-title">{{ txt($scale['chart_title']) }}</figcaption>
                    <p class="chart-subtitle">{{ txt($scale['chart_subtitle']) }}</p>
                    <ul class="bar-chart__rows" role="list">
                        @foreach ($scale['bars'] as $row)
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
                <x-section-heading :eyebrow="txt($scale['eyebrow'])" :title="rich($scale['title'])" />
                <div class="compare-cards" data-compare-cards data-custom-anim>
                    <div class="compare-card" data-reveal-card>
                        <p class="compare-card__label">{{ txt($scale['growth_label']) }}</p>
                        <div class="compare-card__row">
                            <span class="compare-card__from">{{ $scale['growth_from'] }}<small>{{ $scale['growth_from_unit'] }}</small></span>
                            <x-icon name="arrow-right" size="22" />
                            <span class="compare-card__to">@if ($countable($scale['growth_to']))<span data-count="{{ $scale['growth_to'] }}">{{ $scale['growth_to'] }}</span>@else{{ $scale['growth_to'] }}@endif<small>{{ $scale['growth_to_unit'] }}</small></span>
                        </div>
                    </div>
                    <div class="compare-card" data-reveal-card>
                        <p class="compare-card__label">{{ txt($scale['spend_label']) }}</p>
                        <p class="compare-card__big">{{ $scale['spend_value'] }}</p>
                        <p class="compare-card__note">{{ txt($scale['spend_note']) }}</p>
                    </div>
                    <div class="compare-card" data-reveal-card>
                        <p class="compare-card__label">{{ txt($scale['teams_label']) }}</p>
                        <div class="compare-card__row">
                            <span class="compare-card__to">@if ($countable($scale['teams_a']))<span data-count="{{ $scale['teams_a'] }}">{{ $scale['teams_a'] }}</span>@else{{ $scale['teams_a'] }}@endif<small>{{ $scale['teams_a_unit'] }}</small></span>
                            <span class="compare-card__to">@if ($countable($scale['teams_b']))<span data-count="{{ $scale['teams_b'] }}">{{ $scale['teams_b'] }}</span>@else{{ $scale['teams_b'] }}@endif<small>{{ $scale['teams_b_unit'] }}</small></span>
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
        <x-section-heading :eyebrow="txt($c['drivers']['eyebrow'])" :title="rich($c['drivers']['title'])" align="center" />
        <div class="services-grid">
            @foreach ($c['drivers']['items'] as $item)
                <x-service-card :service="['icon' => $item['icon'], 'title' => txt($item['title']), 'text' => txt($item['text'])]" />
            @endforeach
        </div>
    </div>
</section>

{{-- ============ MARKETS ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading :eyebrow="txt($c['markets']['eyebrow'])" :title="rich($c['markets']['title'])" />
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
        <p class="fine-print" data-reveal>{{ txt($c['markets']['fine_print']) }}</p>
    </div>
</section>

<x-cta :title="$c['cta']['title']" :text="$c['cta']['text']" />

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/results.js') }}?v={{ filemtime(public_path('assets/js/pages/results.js')) }}" defer></script>
@endpush
