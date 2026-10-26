@php
    $links = [
        'home' => 'Home',
        'about' => 'About',
        'services' => 'Services',
        'portfolio' => 'Portfolio',
        'results' => 'Results',
        'international' => 'International',
    ];
@endphp

<header class="site-header" data-header>
    <nav class="site-nav container-xl" aria-label="Main">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ config('portfolio.person.name') }}, home">
            <span class="brand__mark" aria-hidden="true">AH</span>
            <span class="brand__text">
                <span class="brand__name">{{ config('portfolio.person.name') }}</span>
                <span class="brand__role">Performance Marketing</span>
            </span>
        </a>

        <div class="site-nav__track" data-nav-track>
            <span class="site-nav__indicator" data-nav-indicator aria-hidden="true"></span>
            <ul class="site-nav__links" role="list">
                @foreach ($links as $route => $label)
                    <li>
                        <a href="{{ route($route) }}" @class(['site-nav__link', 'is-active' => request()->routeIs($route)]) @if (request()->routeIs($route)) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="site-nav__actions">
            <x-button :href="route('contact')" size="sm" class="d-none d-sm-inline-flex">Let's talk</x-button>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
                <span class="visually-hidden" data-menu-label>Open menu</span>
                <span class="menu-toggle__bar"></span>
                <span class="menu-toggle__bar"></span>
            </button>
        </div>
    </nav>

    <div class="mobile-menu" id="mobile-menu" data-mobile-menu hidden>
        <ul class="mobile-menu__links" role="list">
            @foreach ($links + ['contact' => 'Contact'] as $route => $label)
                <li>
                    <a href="{{ route($route) }}" @class(['mobile-menu__link', 'is-active' => request()->routeIs($route)]) @if (request()->routeIs($route)) aria-current="page" @endif>
                        <span class="mobile-menu__index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="mobile-menu__foot">
            <a href="{{ config('portfolio.person.linkedin') }}" target="_blank" rel="noopener">LinkedIn <x-icon name="arrow-up-right" size="16" /></a>
            <span>{{ config('portfolio.person.location') }}</span>
        </div>
    </div>
</header>
