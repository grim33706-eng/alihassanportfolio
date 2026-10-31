<!DOCTYPE html>
<html lang="en-GB" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo')
    <meta name="theme-color" content="#0B1020">

    <script>document.documentElement.classList.replace('no-js', 'js');</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">

</head>
<body data-page="@yield('page', 'default')">
    <a class="skip-link" href="#main">Skip to content</a>

    <div class="page-loader" data-loader aria-hidden="true">
        <div class="page-loader__inner">
            <div class="page-loader__mark">
                <svg class="page-loader__ring" viewBox="0 0 100 100">
                    <defs>
                        <linearGradient id="loader-grad" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#6d93ff" />
                            <stop offset="1" stop-color="#8B5CF6" />
                        </linearGradient>
                    </defs>
                    <circle cx="50" cy="50" r="46" />
                    <circle class="page-loader__arc" cx="50" cy="50" r="46" />
                </svg>
                <span>{{ config('content.site.monogram') }}</span>
            </div>
            <span class="page-loader__bar"><span data-loader-bar></span></span>
            <span class="page-loader__label">{{ config('portfolio.person.name') }}</span>
        </div>
    </div>
    <script>
        // Continue the loader bar from where the previous page left it, before first paint.
        try {
            var lp = JSON.parse(sessionStorage.getItem('loader-progress') || 'null');
            sessionStorage.removeItem('loader-progress');
            if (lp && Date.now() - lp.t < 8000) {
                document.querySelector('[data-loader-bar]').style.transform = 'scaleX(' + lp.p + ')';
                document.querySelector('[data-loader]').dataset.continue = lp.p;
            }
        } catch (e) {}
    </script>

    <div class="scroll-progress" aria-hidden="true"><span data-scroll-progress></span></div>
    <div class="site-backdrop" aria-hidden="true">
        <div class="site-backdrop__grid"></div>
        <div class="site-backdrop__glow site-backdrop__glow--a"></div>
        <div class="site-backdrop__glow site-backdrop__glow--b"></div>
    </div>

    <x-nav />

    <main id="main" tabindex="-1">
        @yield('content')
    </main>

    <x-footer />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js" defer></script>
    <script src="{{ asset('assets/js/app.js') }}?v={{ filemtime(public_path('assets/js/app.js')) }}" defer></script>

    {{-- Three.js is only downloaded when a page's hero scene runs, or an in-page scene scrolls near. --}}
    <script type="importmap">
        { "imports": { "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js" } }
    </script>
    @yield('three')
    <script type="module" src="{{ asset('assets/js/three/sections.js') }}?v={{ filemtime(public_path('assets/js/three/sections.js')) }}"></script>

    @stack('scripts')
</body>
</html>
