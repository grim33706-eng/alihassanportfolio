@extends('layouts.app')

@section('page', 'international')
@section('title', 'International Experience')
@section('description', 'Ali Hassan\'s international advertising experience: Meta, Google and TikTok campaigns for the UK, France, USA, Canada, Australia, the UAE, Saudi Arabia, Oman, Azerbaijan, Uzbekistan, Bangladesh and Pakistan.')

@section('content')

<section class="globe-hero" data-page-hero>
    <div class="container-xl">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5 globe-hero__copy">
                <p class="eyebrow" data-hero-item>International experience</p>
                <h1 class="page-hero__title" data-split="hero">One strategist, <span class="text-gradient">twelve markets.</span></h1>
                <p class="page-hero__lead" data-hero-item>Paid campaigns for e-commerce brands and lead-generation clients across North America, Europe, the Gulf, Central and South Asia and Australia — run from Lahore.</p>
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
        <x-section-heading eyebrow="Markets" title="Where I've run campaigns" lead="Grouped by the type of work done in each market." />
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
            @foreach ([
                ['cart', 'E-commerce sales', 'Australia · Canada · UK · USA', 'Conversion-focused campaigns for fashion, home textiles, jewellery and food brands, including Google Search, Performance Max and remarketing for an international bedding brand. Portfolio average ~4x ROAS.'],
                ['funnel', 'B2B & B2C lead generation', 'USA · Canada', 'Qualified lead generation through Meta, Google and TikTok Ads, with a focus on conversion performance and lead quality.'],
                ['briefcase', 'Service-business leads', 'UK · France', 'Meta and TikTok lead campaigns with tailored targeting and creative tests, on daily budgets of PKR 20,000–40,000 across client accounts, optimised for cost per lead.'],
                ['map-pin', 'Real estate leads', 'Dubai · Azerbaijan · Uzbekistan', 'Real estate and other paid lead campaigns, with creative angles and hooks directed by performance data.'],
                ['meta', 'Meta campaigns', 'Saudi Arabia · UAE · Oman · Bangladesh · Pakistan', 'Meta campaigns for company and client brands, optimised through audience segmentation, budget monitoring and A/B tests.'],
                ['users', 'Local lead generation', 'Pakistan', 'Lead generation for local businesses, managing up to 15 client accounts at the same time.'],
            ] as [$icon, $title, $where, $text])
                <div class="col-md-6 col-xl-4">
                    <div class="intl-card" data-reveal-card data-tilt>
                        <span class="icon-chip"><x-icon :name="$icon" /></span>
                        <h3 class="intl-card__title">{{ $title }}</h3>
                        <p class="intl-card__where">{{ $where }}</p>
                        <p class="intl-card__text">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<x-cta title="Selling into a new market?" text="I've planned audiences, budgets and creative for campaigns across four continents. Let's talk about where you want to grow." />

@endsection

@section('three')
    <script type="module" src="{{ asset('assets/js/three/globe.js') }}?v={{ filemtime(public_path('assets/js/three/globe.js')) }}"></script>
@endsection
