<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | {{ config('portfolio.person.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
</head>
<body class="admin">
    <header class="admin-bar">
        <div class="container-xl admin-bar__inner">
            <a href="{{ route('admin.case-studies.index') }}" class="brand">
                <span class="brand__mark" aria-hidden="true">AH</span>
                <span class="brand__text">
                    <span class="brand__name">Case study admin</span>
                    <span class="brand__role">Before &amp; after results</span>
                </span>
            </a>
            @if (session('is_admin'))
                <div class="admin-bar__actions">
                    <a href="{{ route('results') }}#before-after" target="_blank" rel="noopener" class="btn-ui btn-ui--ghost btn-ui--sm"><span class="btn-ui__label">View on site</span><x-icon name="arrow-up-right" size="16" /></a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn-ui btn-ui--ghost btn-ui--sm"><span class="btn-ui__label">Log out</span></button>
                    </form>
                </div>
            @endif
        </div>
    </header>

    <main class="admin-main container-xl">
        @if (session('status'))
            <div class="form-alert form-alert--success admin-flash"><x-icon name="check" size="20" /> <span>{{ session('status') }}</span></div>
        @endif
        @if ($errors->any())
            <div class="form-alert form-alert--error admin-flash">
                <x-icon name="alert" size="20" />
                <div>
                    <strong>Please fix the following:</strong>
                    <ul class="admin-errors">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
