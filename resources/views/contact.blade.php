@extends('layouts.app')

@php
    $c = config('content.contact');
@endphp

@section('page', 'contact')
@section('title', txt($c['seo_title']))
@section('description', txt($c['seo_description']))

@section('content')

<x-page-hero
    variant="contact"
    :eyebrow="txt($c['hero']['eyebrow'])"
    :title="rich($c['hero']['title'])"
    :lead="txt($c['hero']['lead'])">
    <x-slot:visual>
        <div class="hero-orbits hero-orbits--contact">
            <span></span><span></span><span></span>
        </div>
    </x-slot:visual>
</x-page-hero>

<section class="section section--flush-top">
    <div class="container-xl">
        <div class="row g-4 g-xl-5">
            <div class="col-lg-7">
                <div class="panel" data-reveal-card>
                    <h2 class="panel__title">{{ txt($c['form_title']) }}</h2>
                    <x-contact-form />
                </div>
            </div>
            <div class="col-lg-5">
                <div class="contact-aside">
                    @if ($person['show_email'] && $person['email'])
                        <a class="contact-line" href="mailto:{{ $person['email'] }}" data-reveal-card>
                            <span class="icon-chip icon-chip--sm"><x-icon name="mail" size="20" /></span>
                            <span><small>Email</small>{{ $person['email'] }}</span>
                        </a>
                    @endif
                    @if ($person['show_phone'] && $person['phone'])
                        <a class="contact-line" href="tel:{{ preg_replace('/\s+/', '', $person['phone']) }}" data-reveal-card>
                            <span class="icon-chip icon-chip--sm"><x-icon name="phone" size="20" /></span>
                            <span><small>Phone</small>{{ $person['phone'] }}</span>
                        </a>
                    @endif
                    <a class="contact-line" href="{{ $person['linkedin'] }}" target="_blank" rel="noopener" data-reveal-card>
                        <span class="icon-chip icon-chip--sm"><x-icon name="linkedin" size="20" /></span>
                        <span><small>LinkedIn</small>{{ $person['linkedin_label'] }}</span>
                    </a>
                    <div class="contact-line" data-reveal-card>
                        <span class="icon-chip icon-chip--sm"><x-icon name="map-pin" size="20" /></span>
                        <span><small>Location</small>{{ $person['location'] }} · {{ txt($c['location_suffix']) }}</span>
                    </div>

                    <div class="panel panel--soft" data-reveal-card>
                        <h2 class="panel__title panel__title--sm">{{ txt($c['tips_title']) }}</h2>
                        <ul class="check-list" role="list">
                            @foreach ($c['tips'] as $tip)
                                <li><x-icon name="check" size="16" /> {{ txt($tip) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/contact.js') }}?v={{ filemtime(public_path('assets/js/pages/contact.js')) }}" defer></script>
@endpush
