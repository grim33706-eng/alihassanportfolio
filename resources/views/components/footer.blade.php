@php
    $person = config('portfolio.person');
    $footer = config('content.site.footer');
@endphp

<footer class="site-footer">
    <div class="container-xl">
        <div class="site-footer__top">
            <div>
                <p class="eyebrow">{{ txt($footer['eyebrow']) }}</p>
                <p class="site-footer__headline">{{ rich($footer['headline'], '<br class="d-none d-md-block"> ') }}</p>
            </div>
            <x-button :href="route('contact')" icon="arrow-right">{{ txt($footer['button']) }}</x-button>
        </div>

        <div class="site-footer__grid">
            <div>
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand__mark" aria-hidden="true">{{ config('content.site.monogram') }}</span>
                    <span class="brand__text">
                        <span class="brand__name">{{ $person['name'] }}</span>
                        <span class="brand__role">{{ $person['role'] }}</span>
                    </span>
                </a>
                <p class="site-footer__note">{{ txt($footer['note']) }}</p>
            </div>

            <nav aria-label="Footer">
                <p class="site-footer__label">Explore</p>
                <ul class="site-footer__links" role="list">
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('services') }}">Services</a></li>
                    <li><a href="{{ route('portfolio') }}">Portfolio</a></li>
                    <li><a href="{{ route('results') }}">Results</a></li>
                    <li><a href="{{ route('international') }}">International</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </nav>

            <div>
                <p class="site-footer__label">Connect</p>
                <ul class="site-footer__links" role="list">
                    <li><a href="{{ $person['linkedin'] }}" target="_blank" rel="noopener"><x-icon name="linkedin" size="16" /> LinkedIn</a></li>
                    @if ($person['show_email'] && $person['email'])
                        <li><a href="mailto:{{ $person['email'] }}"><x-icon name="mail" size="16" /> Email</a></li>
                    @endif
                    <li><span><x-icon name="map-pin" size="16" /> {{ $person['location'] }}</span></li>
                </ul>
            </div>
        </div>

        <div class="site-footer__bottom">
            <span>&copy; {{ date('Y') }} {{ $person['name'] }}</span>
            <span>{{ txt($footer['disclaimer']) }}</span>
        </div>
    </div>
</footer>
