@extends('layouts.app')

@section('page', 'about')
@section('title', 'About')
@section('description', 'About Ali Hassan: 4+ years in performance marketing, from local lead generation to leading PPC and marketing teams running Meta, Google and TikTok Ads for international brands.')

@section('content')

<x-page-hero
    variant="about"
    eyebrow="About me"
    title="Four years of turning <span class='text-gradient'>ad budgets</span> into measurable growth."
    lead="From managing 15 local lead-generation accounts to leading PPC and marketing teams for brands across North America, Europe, the Gulf and Asia-Pacific.">
    <x-slot:visual>
        <div class="hero-orbits">
            <span></span><span></span><span></span>
        </div>
    </x-slot:visual>
    <x-button :href="route('contact')" icon="arrow-right">Work with me</x-button>
    <x-button :href="$person['linkedin']" variant="ghost" icon="arrow-up-right" target="_blank" rel="noopener">LinkedIn</x-button>
</x-page-hero>

{{-- ============ STORY ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-lg-7">
                <x-section-heading eyebrow="Background" title="A performance marketer who leads with data — and with people." />
                <div class="prose prose--lg" data-reveal>
                    @foreach ($person['summary'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-5">
                <div class="fact-stack">
                    @foreach ([
                        ['briefcase', 'Current focus', 'PPC Team Lead at Dynasty Marketing Agency, Performance Marketing Manager at Zela Boost, plus paid and social media at SuperWise International and Chapter001'],
                        ['users', 'Leadership', 'Seven-person PPC team; cross-functional marketing teams of 10+'],
                        ['globe', 'Markets', 'USA, Canada, UK, France, Australia, UAE, Saudi Arabia, Oman, Azerbaijan, Uzbekistan, Bangladesh and Pakistan'],
                        ['map-pin', 'Based in', $person['location']],
                    ] as [$icon, $label, $value])
                        <div class="fact" data-reveal-card>
                            <span class="icon-chip icon-chip--sm"><x-icon :name="$icon" size="20" /></span>
                            <div>
                                <p class="fact__label">{{ $label }}</p>
                                <p class="fact__value">{{ $value }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ SPECIALISATION ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading eyebrow="Specialisation" title="Meta Ads and Google Ads, run as one system" align="center" />
        <div class="row g-4">
            <div class="col-md-6">
                <div class="spec-card spec-card--meta" data-reveal-card data-tilt>
                    <span class="icon-chip"><x-icon name="meta" /></span>
                    <h3 class="spec-card__title">Meta Ads</h3>
                    <p>E-commerce sales for fashion, home textiles, jewellery and food brands, plus B2B and B2C lead generation — with audience segmentation, remarketing and constant creative testing.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="spec-card spec-card--google" data-reveal-card data-tilt>
                    <span class="icon-chip"><x-icon name="google" /></span>
                    <h3 class="spec-card__title">Google Ads</h3>
                    <p>Search, Display, Performance Max and remarketing — backed by keyword research, bidding strategy, ad copywriting and Quality Score and CTR improvements.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ APPROACH ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading eyebrow="Approach" title="How I run a campaign" lead="The same loop, whether it's one account or a sixteen-brand portfolio." />
        <ol class="process" role="list" data-process>
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

{{-- ============ EXPERIENCE TIMELINE ============ --}}
<section class="section" id="experience">
    <div class="container-xl">
        <x-section-heading eyebrow="Experience" title="Career timeline" />
        <ol class="timeline" role="list" data-timeline>
            <span class="timeline__rail" aria-hidden="true"><span data-timeline-fill></span></span>
            @foreach ($experience as $job)
                <li class="timeline__item" data-timeline-item>
                    <span class="timeline__dot" aria-hidden="true"></span>
                    <div class="timeline__card">
                        <div class="timeline__head">
                            <div>
                                <h3 class="timeline__role">{{ $job['role'] }}</h3>
                                <p class="timeline__company">{{ $job['company'] }} <span>· {{ $job['mode'] }}</span></p>
                            </div>
                            <span class="timeline__period">{{ $job['period'] }}</span>
                        </div>
                        <ul class="timeline__list" role="list">
                            @foreach ($job['highlights'] as $highlight)
                                <li>{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============ SKILLS & EDUCATION ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-lg-8">
                <x-section-heading eyebrow="Skills" title="Professional skills" />
                @include('partials.skill-groups', ['skills' => $skills, 'columns' => 2])
            </div>
            <div class="col-lg-4">
                <x-section-heading eyebrow="Education" title="Qualifications" />
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

<x-cta title="Let's talk about your next campaign." />

@endsection
