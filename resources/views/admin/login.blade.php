<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Log in · {{ config('portfolio.person.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
</head>
<body class="adm-body adm-login">
    <div class="adm-login__glow" aria-hidden="true"></div>
    <main class="adm-login__card">
        <div class="adm-login__brand">
            <span class="brand__mark" aria-hidden="true">{{ config('content.site.monogram') }}</span>
            <div>
                <p class="adm-login__name">{{ config('portfolio.person.name') }}</p>
                <p class="adm-login__sub">Site admin</p>
            </div>
        </div>

        <h1 class="adm-login__title">Welcome back</h1>
        <p class="adm-login__lead">Log in to edit your portfolio's pages, projects and case studies.</p>

        @if (session('status'))
            <div class="adm-flash adm-flash--ok" role="status"><x-icon name="check" size="18" /> <span>{{ session('status') }}</span></div>
        @endif

        <form method="POST" action="{{ route('admin.login.attempt') }}" class="adm-login__form">
            @csrf
            <label class="adm-label" for="password">Password</label>
            <div class="adm-password">
                <input class="adm-input @error('password') is-invalid @enderror" type="password" id="password" name="password" required autofocus autocomplete="current-password">
                <button type="button" class="adm-password__toggle" data-password-toggle aria-label="Show password"><x-icon name="search" size="16" /></button>
            </div>
            @error('password')<p class="adm-error">{{ $message }}</p>@enderror
            <button type="submit" class="adm-btn adm-btn--primary adm-btn--block">Log in <x-icon name="arrow-right" size="16" /></button>
        </form>

        <a href="{{ route('home') }}" class="adm-login__back"><x-icon name="arrow-right" size="14" class="flip-x" /> Back to the website</a>
    </main>
    <script>
        document.querySelector('[data-password-toggle]').addEventListener('click', function () {
            var input = document.getElementById('password');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    </script>
</body>
</html>
