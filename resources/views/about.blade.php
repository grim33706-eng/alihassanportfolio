@extends('layouts.app')

@php
    $c = config('content.about');
@endphp

@section('page', 'about')
@section('title', txt($c['seo_title']))
@section('description', txt($c['seo_description']))

@section('content')

<x-page-hero
    variant="about"
    :eyebrow="txt($c['hero']['eyebrow'])"
    :title="rich($c['hero']['title'])"
    :lead="txt($c['hero']['lead'])"
    :image="$c['hero']['image'] ?? null">
    <x-slot:visual>
        {{-- 3D expertise system (about-scene.js): one orbit per skill group; the CSS orbits are the fallback. --}}
        <div class="services-scene about-scene" data-about-scene
            data-groups="{{ json_encode(array_keys($skills)) }}" data-monogram="{{ txt(config('content.site.monogram')) }}">
            <div class="hero-orbits">
                <span></span><span></span><span></span>
            </div>
            <div class="services-scene__labels" data-scene-labels></div>
        </div>
    </x-slot:visual>
    <x-button :href="route('contact')" icon="arrow-right">{{ txt($c['hero']['primary']) }}</x-button>
    <x-button :href="$person['linkedin']" variant="ghost" icon="arrow-up-right" target="_blank" rel="noopener">{{ txt($c['hero']['secondary']) }}</x-button>
</x-page-hero>

{{-- ============ STORY ============ --}}
<section class="section">
    <div class="container-xl">
        <div class="row g-5">
            <div class="col-lg-7">
                <x-section-heading :eyebrow="txt($c['background']['eyebrow'])" :title="rich($c['background']['title'])" />
                <div class="prose prose--lg" data-reveal>
                    @foreach ($person['summary'] as $paragraph)
                        <p>{{ txt($paragraph) }}</p>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-5">
                <div class="fact-stack">
                    @foreach ($c['background']['facts'] as $fact)
                        <div class="fact" data-reveal-card>
                            <span class="icon-chip icon-chip--sm"><x-icon :name="$fact['icon']" size="20" /></span>
                            <div>
                                <p class="fact__label">{{ txt($fact['label']) }}</p>
                                <p class="fact__value">{{ txt($fact['value']) }}</p>
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
        <x-section-heading :eyebrow="txt($c['specialisation']['eyebrow'])" :title="rich($c['specialisation']['title'])" align="center" />
        <div class="row g-4">
            @foreach ($c['specialisation']['cards'] as $card)
                <div class="col-md-6">
                    <div class="spec-card spec-card--{{ $card['icon'] }}" data-reveal-card data-tilt>
                        <span class="icon-chip"><x-icon :name="$card['icon']" /></span>
                        <h3 class="spec-card__title">{{ txt($card['title']) }}</h3>
                        <p>{{ txt($card['text']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ APPROACH ============ --}}
<section class="section">
    <div class="container-xl">
        <x-section-heading :eyebrow="txt($c['approach']['eyebrow'])" :title="rich($c['approach']['title'])" :lead="txt($c['approach']['lead'])" />
        <ol class="process" role="list" data-process>
            @foreach ($process as $step)
                @php $n = str_pad($loop->iteration, 2, '0', STR_PAD_LEFT); @endphp
                <li class="process__step" data-reveal-card>
                    <span class="process__bar" aria-hidden="true"></span>
                    <span class="process__ghost" aria-hidden="true">{{ $n }}</span>
                    <div class="process__head">
                        <span class="process__num">{{ $n }}</span>
                        <span class="process__label">Step</span>
                    </div>
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
        <x-section-heading :eyebrow="txt($c['experience']['eyebrow'])" :title="rich($c['experience']['title'])" />
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
                <x-section-heading :eyebrow="txt($c['skills']['eyebrow'])" :title="rich($c['skills']['title'])" />
                @include('partials.skill-groups', ['skills' => $skills, 'columns' => 2])
            </div>
            <div class="col-lg-4">
                <x-section-heading :eyebrow="txt($c['education']['eyebrow'])" :title="rich($c['education']['title'])" />
                @include('partials.education-list', ['education' => $education, 'certifications' => $certifications])
            </div>
        </div>
    </div>
</section>

<x-cta :title="$c['cta']['title']" :text="$c['cta']['text']" />

@endsection

@section('three')
    @if (empty($c['hero']['image']))
    <script type="module" src="{{ asset('assets/js/three/about-scene.js') }}?v={{ filemtime(public_path('assets/js/three/about-scene.js')) }}"></script>
    @endif
@endsection
