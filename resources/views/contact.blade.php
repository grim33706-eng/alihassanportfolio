@extends('layouts.app')

@section('page', 'contact')
@section('title', 'Contact')
@section('description', 'Contact Ali Hassan, Performance Marketing Manager, about Meta Ads and Google Ads projects or professional opportunities.')

@section('content')

<x-page-hero
    variant="contact"
    eyebrow="Contact"
    title="Let's talk about <span class='text-gradient'>growing your results.</span>"
    lead="Recruiting for a performance marketing role, or looking for someone to run your Meta and Google Ads? Send a message and I'll get back to you.">
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
                    <h2 class="panel__title">Send a message</h2>
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
                        <span><small>Location</small>{{ $person['location'] }} · Remote &amp; on-site</span>
                    </div>

                    <div class="panel panel--soft" data-reveal-card>
                        <h2 class="panel__title panel__title--sm">Good things to include</h2>
                        <ul class="check-list" role="list">
                            <li><x-icon name="check" size="16" /> Your business or the role you're hiring for</li>
                            <li><x-icon name="check" size="16" /> Platforms you're using or considering</li>
                            <li><x-icon name="check" size="16" /> Target markets and main goal — sales or leads</li>
                            <li><x-icon name="check" size="16" /> Rough monthly budget, if known</li>
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
