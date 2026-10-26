@extends('layouts.app')

@section('page', 'services')
@section('title', 'Services')
@section('description', 'Paid advertising services from Ali Hassan: Meta Ads and Google Ads management, campaign strategy, audience and keyword research, conversion tracking with GTM and GA4, and optimisation and reporting.')

@section('content')

<x-page-hero
    variant="services"
    eyebrow="Services"
    title="Paid advertising, <span class='text-gradient'>managed end to end.</span>"
    lead="Strategy, setup, tracking, creative testing and optimisation for e-commerce sales and B2B/B2C lead generation.">
    <x-slot:visual>
        <div class="hero-tiles">
            @for ($i = 0; $i < 12; $i++)<span></span>@endfor
        </div>
    </x-slot:visual>
    <x-button href="#services-list" icon="arrow-right">Explore services</x-button>
</x-page-hero>

<section class="section" id="services-list">
    <div class="container-xl">
        <div class="services-grid services-grid--lg" data-custom-anim data-anim="flip-cards">
            @foreach ($services as $service)
                <x-service-card :service="$service" :index="$loop->iteration" id="{{ $service['slug'] }}" />
            @endforeach
        </div>
    </div>
</section>

{{-- ============ PLATFORM DETAIL ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading eyebrow="By platform" title="What's included on each platform" align="center" />
        <div class="row g-4" data-custom-anim data-anim="sheets">
            @foreach ($platforms as $key => $platform)
                <div class="col-lg-6">
                    <div class="platform-sheet platform-sheet--{{ $key }}" data-reveal-card>
                        <div class="platform-sheet__head">
                            <span class="icon-chip"><x-icon :name="$key" /></span>
                            <div>
                                <h3 class="platform-sheet__title">{{ $platform['name'] }}</h3>
                                <p class="platform-sheet__tagline">{{ $platform['tagline'] }}</p>
                            </div>
                        </div>
                        <ul class="platform-sheet__list" role="list">
                            @foreach ($platform['services'] as $service)
                                <li>
                                    <x-icon :name="$service['icon']" size="18" />
                                    <div>
                                        <strong>{{ $service['title'] }}</strong>
                                        <span>{{ $service['text'] }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="also-runs also-runs--center" data-reveal>
            Also available: @foreach ($alsoRuns as $item)<span>{{ $item }}</span>@endforeach
        </p>
    </div>
</section>

{{-- ============ PROCESS ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading eyebrow="Working together" title="From tracking setup to scale" />
        <ol class="process" role="list" data-process data-custom-anim data-anim="process">
            <span class="process__line" aria-hidden="true"><span data-process-fill></span></span>
            @foreach ($process as $step)
                <li class="process__step" data-reveal-card>
                    <span class="process__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="process__title">{{ $step['title'] }}</h3>
                    <p class="process__text">{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============ WHO FOR ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading eyebrow="Industries" title="Experience across sectors" />
        <ul class="industry-grid" role="list" data-custom-anim data-anim="industries">
            @foreach ([
                ['cart', 'E-commerce', 'Fashion, home textiles, bedding, jewellery and food brands.'],
                ['map-pin', 'Real estate', 'Paid lead campaigns in Dubai, Azerbaijan and Uzbekistan, plus real estate accounts in a multi-brand portfolio.'],
                ['display', 'Software', 'Lead-generation campaigns for software businesses.'],
                ['briefcase', 'Service businesses', 'B2B and B2C leads for service businesses and local companies.'],
            ] as [$icon, $title, $text])
                <li class="industry" data-reveal-card>
                    <x-icon :name="$icon" size="22" />
                    <h3 class="industry__title">{{ $title }}</h3>
                    <p class="industry__text">{{ $text }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>

<x-cta title="Need a hand with your ad accounts?" />

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/services.js') }}?v={{ filemtime(public_path('assets/js/pages/services.js')) }}" defer></script>
@endpush
