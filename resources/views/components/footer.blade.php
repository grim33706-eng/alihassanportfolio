@php
    $person = config('portfolio.person');
@endphp

<footer class="site-footer">
    <div class="container-xl">
        <div class="site-footer__top">
            <div>
                <p class="eyebrow">Open to new projects &amp; roles</p>
                <p class="site-footer__headline">Let's make your ad spend<br class="d-none d-md-block"> work harder.</p>
            </div>
            <x-button :href="route('contact')" icon="arrow-right">Start a conversation</x-button>
        </div>

        <div class="site-footer__grid">
            <div>
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand__mark" aria-hidden="true">AH</span>
                    <span class="brand__text">
                        <span class="brand__name">{{ $person['name'] }}</span>
                        <span class="brand__role">{{ $person['role'] }}</span>
                    </span>
                </a>
                <p class="site-footer__note">Meta Ads, Google Ads &amp; TikTok Ads for e-commerce sales and B2B/B2C lead generation.</p>
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
            <span>Figures come from documented work history and reflect past performance, not guaranteed results.</span>
        </div>
    </div>
</footer>
