@extends('layouts.app')

@php
    $c = config('content.international');
@endphp

@section('page', 'international')
@section('title', txt($c['seo_title']))
@section('description', txt($c['seo_description']))

@section('content')

<section class="globe-hero" data-page-hero>
    <div class="container-xl">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5 globe-hero__copy">
                <p class="eyebrow" data-hero-item>{{ txt($c['hero']['eyebrow']) }}</p>
                <h1 class="page-hero__title" data-split="hero">{{ rich($c['hero']['title']) }}</h1>
                <p class="page-hero__lead" data-hero-item>{{ txt($c['hero']['lead']) }}</p>
                <ul class="region-pills" role="list" data-hero-item>
                    @foreach (collect($markets)->pluck('region')->unique() as $region)
                        <li>{{ $region }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-7">
                <div class="globe" data-globe data-markets="{{ $globeMarkets->toJson() }}" aria-hidden="true">
                    <div class="globe__fallback">
                        <x-icon name="globe" size="120" />
                    </div>
                    <div class="globe__labels" data-globe-labels></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--flush-top">
    <div class="container-xl">
        <x-section-heading :eyebrow="txt($c['markets']['eyebrow'])" :title="rich($c['markets']['title'])" :lead="txt($c['markets']['lead'])" />
        <div class="market-grid">
            @foreach ($markets as $market)
                <article class="market-card" data-reveal-card data-market-card="{{ $market['code'] }}">
                    <div class="market-card__head">
                        <span class="market-card__code">{{ $market['code'] }}</span>
                        <span class="market-card__region">{{ $market['region'] }}</span>
                    </div>
                    <h3 class="market-card__name">{{ $market['name'] }}</h3>
                    <p class="market-card__focus">{{ $market['focus'] }}</p>
                    <p class="market-card__work">{{ $market['work'] }}</p>
                    <x-platform-badges :platforms="$market['platforms']" />
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container-xl">
        <div class="row g-4">
            @foreach ($c['cards'] as $card)
                <div class="col-md-6 col-xl-4">
                    <div class="intl-card" data-reveal-card data-tilt>
                        <span class="icon-chip"><x-icon :name="$card['icon']" /></span>
                        <h3 class="intl-card__title">{{ txt($card['title']) }}</h3>
                        <p class="intl-card__where">{{ txt($card['where']) }}</p>
                        <p class="intl-card__text">{{ txt($card['text']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<x-cta :title="$c['cta']['title']" :text="$c['cta']['text']" />

@endsection

@section('three')
    <script type="module" src="{{ asset('assets/js/three/globe.js') }}?v={{ filemtime(public_path('assets/js/three/globe.js')) }}"></script>
@endsection
