<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') · {{ config('portfolio.person.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
</head>
<body class="adm-body">
@php
    $sections = collect(config('admin.sections'));
    $unread = rescue(fn () => \App\Models\ContactMessage::whereNull('read_at')->count(), 0, false);
    $current = request()->route('section');
    $monogram = config('content.site.monogram');
@endphp

<a class="skip-link" href="#adm-main">Skip to content</a>

<div class="adm-shell">
    {{-- ============ Sidebar ============ --}}
    <aside class="adm-sidebar" id="adm-sidebar" data-adm-sidebar>
        <a href="{{ route('admin.dashboard') }}" class="adm-brand">
            <span class="brand__mark" aria-hidden="true">{{ $monogram }}</span>
            <span class="adm-brand__text">
                <span class="adm-brand__name">{{ config('portfolio.person.name') }}</span>
                <span class="adm-brand__role">Site admin</span>
            </span>
        </a>

        <nav class="adm-nav" aria-label="Admin">
            <a href="{{ route('admin.dashboard') }}" @class(['adm-nav__link', 'is-active' => request()->routeIs('admin.dashboard')])>
                <x-icon name="gauge" size="18" /> Dashboard
            </a>

            @foreach (config('admin.groups') as $group)
                <p class="adm-nav__group">{{ $group }}</p>
                @foreach ($sections->where('group', $group) as $id => $section)
                    <a href="{{ route('admin.content.edit', $id) }}" @class(['adm-nav__link', 'is-active' => $current === $id])>
                        <x-icon :name="$section['icon']" size="18" /> {{ $section['label'] }}
                    </a>
                @endforeach
            @endforeach

            <p class="adm-nav__group">Manage</p>
            <a href="{{ route('admin.case-studies.index') }}" @class(['adm-nav__link', 'is-active' => request()->routeIs('admin.case-studies.*')])>
                <x-icon name="columns" size="18" /> Case studies
            </a>
            <a href="{{ route('admin.messages.index') }}" @class(['adm-nav__link', 'is-active' => request()->routeIs('admin.messages.*')])>
                <x-icon name="mail" size="18" /> Messages
                @if ($unread)<span class="adm-badge">{{ $unread }}</span>@endif
            </a>
            <a href="{{ route('admin.settings') }}" @class(['adm-nav__link', 'is-active' => request()->routeIs('admin.settings*')])>
                <x-icon name="compass" size="18" /> Settings
            </a>
        </nav>

        <div class="adm-sidebar__foot">
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="adm-nav__link">
                <x-icon name="arrow-up-right" size="18" /> View live site
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="adm-nav__link adm-nav__link--btn"><x-icon name="arrow-right" size="18" class="flip-x" /> Log out</button>
            </form>
        </div>
    </aside>
    <div class="adm-scrim" data-adm-scrim hidden></div>

    {{-- ============ Main ============ --}}
    <div class="adm-main">
        <header class="adm-topbar">
            <button type="button" class="adm-menu" data-adm-menu aria-controls="adm-sidebar" aria-expanded="false" aria-label="Open menu">
                <span></span><span></span>
            </button>
            <div class="adm-topbar__title">
                <span class="adm-topbar__crumb">@yield('crumb', 'Admin')</span>
                <span class="adm-topbar__page">@yield('title', 'Dashboard')</span>
            </div>
            <div class="adm-topbar__actions">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="adm-btn adm-btn--ghost adm-btn--sm d-none d-md-inline-flex">
                    View site <x-icon name="arrow-up-right" size="15" />
                </a>
                <span class="adm-user" title="{{ config('portfolio.person.name') }}">
                    @if (! empty(config('portfolio.person.photo')))
                        <img src="{{ media_url(config('portfolio.person.photo')) }}" alt="">
                    @else
                        {{ $monogram }}
                    @endif
                </span>
            </div>
        </header>

        <main class="adm-content" id="adm-main" tabindex="-1">
            @if (session('status'))
                <div class="adm-flash adm-flash--ok" role="status" data-flash>
                    <x-icon name="check" size="18" /> <span>{{ session('status') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="adm-flash adm-flash--error" role="alert">
                    <x-icon name="alert" size="18" />
                    <div>
                        <strong>Please fix the following:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('assets/js/admin.js') }}?v={{ filemtime(public_path('assets/js/admin.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
