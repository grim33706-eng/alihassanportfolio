{{--
    Everything search engines and link previews read: title, description, keywords,
    canonical, Open Graph / Twitter tags, verification codes and schema.org data
    (WebSite, Person, ProfessionalService, the page itself and its breadcrumb).
    All of it comes from the content editable in the admin.
--}}
@php
    $person = config('portfolio.person');
    $site = config('content.site');
    $seo = $site['seo'] ?? [];
    $name = $person['name'];
    $page = trim($__env->yieldContent('page', 'default'));

    // "Title | Name", unless the page title already carries the name.
    $decode = fn ($s) => html_entity_decode(trim((string) $s), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $pageTitle = $decode($__env->yieldContent('title'));
    $fullTitle = $pageTitle === '' ? $name.' · '.$person['role']
        : (str_contains($pageTitle, $name) ? $pageTitle : $pageTitle.' | '.$name);
    $description = $decode($__env->yieldContent('description')) ?: txt($person['intro']);
    $keywords = collect($seo['keywords'] ?? [])->map(fn ($k) => txt($k))->filter()->implode(', ');
    $url = url()->current();

    $shareImage = ! empty($site['share_image']) ? media_url($site['share_image']) : asset('og-image.png');
    $photo = ! empty($person['photo']) ? media_url($person['photo']) : $shareImage;

    $labels = ['about' => 'About', 'services' => 'Services', 'portfolio' => 'Portfolio', 'results' => 'Results', 'international' => 'International experience', 'contact' => 'Contact'];
    $pageType = ['about' => 'AboutPage', 'contact' => 'ContactPage', 'portfolio' => 'CollectionPage'][$page] ?? 'WebPage';

    $home = url('/').'/';
    $ids = ['site' => $home.'#website', 'person' => $home.'#person', 'service' => $home.'#service', 'page' => $url.'#webpage'];
    $location = trim(explode(',', (string) $person['location'])[0]);
    $knowsAbout = ['Performance marketing', 'Meta Ads', 'Facebook Ads', 'Instagram Ads', 'Google Ads', 'Google Search Ads', 'Performance Max', 'TikTok Ads', 'Snapchat Ads', 'PPC', 'Paid social', 'Conversion tracking', 'Google Tag Manager', 'Google Analytics 4', 'Meta Pixel', 'E-commerce advertising', 'Lead generation', 'A/B testing', 'Remarketing'];
    $current = collect(config('portfolio.experience'))
        ->filter(fn ($e) => str_contains($e['period'] ?? '', 'Present'))
        ->map(fn ($e) => ['@type' => 'Organization', 'name' => $e['company']])->values()->all();
    $education = collect(config('portfolio.education'))->take(1)
        ->map(fn ($e) => ['@type' => 'CollegeOrUniversity', 'name' => $e['school']])->values()->all();

    $graph = [
        [
            '@type' => 'WebSite',
            '@id' => $ids['site'],
            'url' => $home,
            'name' => $name,
            'alternateName' => collect($seo['alternate_names'] ?? [])->map(fn ($n) => txt($n))->filter()->values()->all(),
            'description' => txt(config('content.home.seo_description')),
            'inLanguage' => 'en',
            'publisher' => ['@id' => $ids['person']],
        ],
        array_filter([
            '@type' => 'Person',
            '@id' => $ids['person'],
            'name' => $name,
            'givenName' => $person['first_name'] ?? null,
            'url' => $home,
            'image' => $photo,
            'jobTitle' => $person['role'],
            'description' => txt($person['intro']),
            'email' => ($person['show_email'] && $person['email']) ? 'mailto:'.$person['email'] : null,
            'telephone' => ($person['show_phone'] && $person['phone']) ? $person['phone'] : null,
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $location, 'addressCountry' => 'PK'],
            'knowsAbout' => $knowsAbout,
            'knowsLanguage' => ['English', 'Urdu'],
            'worksFor' => $current ?: null,
            'alumniOf' => $education ?: null,
            'sameAs' => array_values(array_filter([$person['linkedin'] ?? null])),
        ]),
        [
            '@type' => 'ProfessionalService',
            '@id' => $ids['service'],
            'name' => $name.' — Meta Ads & Google Ads Expert',
            'url' => $home,
            'image' => $shareImage,
            'description' => txt(config('content.services.seo_description')),
            'founder' => ['@id' => $ids['person']],
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $location, 'addressCountry' => 'PK'],
            'areaServed' => collect(config('portfolio.markets'))->map(fn ($m) => ['@type' => 'Country', 'name' => $m['name']])->values()->all(),
            'knowsAbout' => $knowsAbout,
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Paid advertising services',
                'itemListElement' => collect(config('portfolio.services'))->map(fn ($s) => [
                    '@type' => 'Offer',
                    'itemOffered' => ['@type' => 'Service', 'name' => $s['title'], 'description' => $s['summary'] ?? ($s['text'] ?? null)],
                ])->values()->all(),
            ],
        ],
        array_filter([
            '@type' => $pageType,
            '@id' => $ids['page'],
            'url' => $url,
            'name' => $fullTitle,
            'description' => $description,
            'isPartOf' => ['@id' => $ids['site']],
            'about' => ['@id' => $ids['person']],
            'inLanguage' => 'en',
            'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $shareImage],
            'breadcrumb' => isset($labels[$page]) ? ['@id' => $url.'#breadcrumb'] : null,
        ]),
    ];
    if (isset($labels[$page])) {
        $graph[] = [
            '@type' => 'BreadcrumbList',
            '@id' => $url.'#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $labels[$page], 'item' => $url],
            ],
        ];
    }
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description }}">
@if ($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif
<meta name="author" content="{{ $name }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
<link rel="canonical" href="{{ $url }}">
@if (! empty($seo['google_verification']))
    <meta name="google-site-verification" content="{{ $seo['google_verification'] }}">
@endif
@if (! empty($seo['bing_verification']))
    <meta name="msvalidate.01" content="{{ $seo['bing_verification'] }}">
@endif

<meta property="og:type" content="{{ $page === 'home' ? 'profile' : 'website' }}">
<meta property="og:site_name" content="{{ $name }} · {{ $person['role'] }}">
<meta property="og:locale" content="en_GB">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $name }} — {{ $person['role'] }}">
@if ($page === 'home')
    <meta property="profile:first_name" content="{{ $person['first_name'] ?? $name }}">
    <meta property="profile:last_name" content="{{ trim(\Illuminate\Support\Str::after($name, $person['first_name'] ?? '')) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $shareImage }}">

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="icon" href="{{ asset('favicon-48.png') }}" type="image/png" sizes="48x48">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@stack('schema')
