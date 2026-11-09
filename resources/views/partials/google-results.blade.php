{{--
    Google Ads account reports (config: portfolio.google_results, edited under
    Content > Google Ads results). Figures are shown exactly as the reports round them.
--}}
@php
    $g = config('portfolio.google_results');
    $accounts = collect($g['accounts'] ?? [])->filter(fn ($a) => ! empty($a['image']))->values();
    // Real dimensions reserve the space, so lazy images don't shift the page.
    $imageSize = fn ($path) => @getimagesize(str_starts_with($path, 'assets/') ? public_path($path) : storage_path('app/public/'.$path)) ?: [null, null];
@endphp

@if ($accounts->isNotEmpty())
<section class="section" id="google-results" data-google-results data-custom-anim>
    <div class="container-xl">
        <x-section-heading :eyebrow="txt($g['eyebrow'])" :title="rich($g['title'])" :lead="txt($g['lead'])" />

        <div class="google-accounts">
            @foreach ($accounts as $account)
                @php
                    [$width, $height] = $imageSize($account['image']);
                    $url = \Illuminate\Support\Str::startsWith($account['url'] ?? '', ['http://', 'https://']) ? $account['url'] : null;
                @endphp
                <article class="google-account" data-google-account>
                    <header class="google-account__head">
                        <div>
                            <p class="meta-chip"><x-icon name="google" size="16" /> {{ txt($account['period']) }}</p>
                            <h3 class="google-account__name">{{ txt($account['name']) }}</h3>
                        </div>
                        @if ($url)
                            <a class="google-account__link" href="{{ $url }}" target="_blank" rel="noopener">Visit website <x-icon name="arrow-up-right" size="15" /></a>
                        @endif
                    </header>

                    <dl class="google-account__metrics">
                        @foreach ($account['metrics'] ?? [] as $metric)
                            <div><dt>{{ txt($metric['label']) }}</dt><dd>{{ txt($metric['value']) }}</dd></div>
                        @endforeach
                    </dl>

                    <figure class="meta-shot">
                        <figcaption class="meta-shot__bar">
                            <span class="meta-shot__dots" aria-hidden="true"><i></i><i></i><i></i></span>
                            <span class="meta-shot__label">Account overview</span>
                            <span class="meta-shot__source">Google Ads</span>
                        </figcaption>
                        <a class="meta-shot__link" href="{{ media_url($account['image']) }}" target="_blank" rel="noopener" data-lightbox-open="google-reports" data-lightbox-index="{{ $loop->index }}" aria-label="View the {{ txt($account['name']) }} report full size">
                            <img src="{{ media_url($account['image']) }}" alt="Google Ads overview for {{ txt($account['name']) }}, {{ txt($account['period']) }}" loading="lazy" decoding="async" @if ($width) width="{{ $width }}" height="{{ $height }}" @endif>
                        </a>
                    </figure>
                </article>
            @endforeach
        </div>

        <template data-lightbox-group="google-reports">
            @foreach ($accounts as $account)
                <a href="{{ media_url($account['image']) }}" data-label="Report" data-caption="{{ txt($account['name']) }}, {{ txt($account['period']) }}"></a>
            @endforeach
        </template>

        <p class="chart-note meta-note">{{ txt($g['note']) }}</p>
    </div>
</section>
@endif
