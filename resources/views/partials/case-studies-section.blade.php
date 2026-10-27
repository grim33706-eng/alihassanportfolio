{{--
    Before & After case studies.
    $caseStudies — collection of CaseStudy; $full — true for the filterable list,
    false for a featured strip; $eyebrow, $title, $lead — heading copy.
--}}
@php
    $full = $full ?? false;
    $hasDemo = $caseStudies->contains('is_demo', true);
    $copy = config('content.site.case_studies');
@endphp

@if ($caseStudies->isNotEmpty())
<section class="section case-studies-section" id="{{ $full ? 'before-after' : 'before-after-featured' }}" data-case-studies>
    <div class="container-xl">
        <div class="section-head-row">
            <x-section-heading :eyebrow="txt($eyebrow ?? $copy['eyebrow'])" :title="rich($title ?? $copy['title'])" :lead="isset($lead) ? txt($lead) : null" />
            @unless ($full)
                <x-button :href="route('results').'#before-after'" variant="ghost" icon="arrow-right" data-reveal>{{ txt($copy['all_button']) }}</x-button>
            @endunless
        </div>

        @if ($hasDemo)
            <div class="demo-notice" data-reveal>
                <x-icon name="alert" size="18" />
                <p><strong>Demonstration layout.</strong> {{ txt($copy['demo_notice']) }}</p>
            </div>
        @endif

        @if ($full)
            @php
                $platformKeys = $caseStudies->pluck('platforms')->flatten()->unique()->values();
                $categoryKeys = $caseStudies->pluck('category')->unique()->values();
            @endphp
            <div class="filters filters--compact" data-cs-filters>
                <div class="filters__group" role="group" aria-label="Filter case studies by platform">
                    <span class="filters__label">Platform</span>
                    <button type="button" class="filter-chip is-active" data-cs-filter="platform" data-value="all" aria-pressed="true">All</button>
                    @foreach ($platformKeys as $key)
                        <button type="button" class="filter-chip" data-cs-filter="platform" data-value="{{ $key }}" aria-pressed="false">
                            <x-icon :name="$key" size="16" /> {{ config('portfolio.platform_labels.'.$key, ucfirst($key)) }}
                        </button>
                    @endforeach
                </div>
                <div class="filters__group" role="group" aria-label="Filter case studies by industry">
                    <span class="filters__label">Category</span>
                    <button type="button" class="filter-chip is-active" data-cs-filter="category" data-value="all" aria-pressed="true">All</button>
                    @foreach ($categoryKeys as $key)
                        <button type="button" class="filter-chip" data-cs-filter="category" data-value="{{ $key }}" aria-pressed="false">{{ config('portfolio.categories.'.$key, ucfirst($key)) }}</button>
                    @endforeach
                </div>
                <p class="filters__count" aria-live="polite" data-cs-count>Showing {{ $caseStudies->count() }} of {{ $caseStudies->count() }}</p>
            </div>
        @endif

        <div @class(['case-studies', 'case-studies--grid' => ! $full])>
            @foreach ($caseStudies as $caseStudy)
                <x-case-study :case-study="$caseStudy" :compact="! $full" />
            @endforeach
        </div>

        @if ($full)
            <div class="empty-state" data-cs-empty hidden>
                <x-icon name="search" size="28" />
                <p>No case studies match those filters.</p>
            </div>
        @endif
    </div>
</section>

@once
    @push('scripts')
        <script src="{{ asset('assets/js/case-studies.js') }}?v={{ filemtime(public_path('assets/js/case-studies.js')) }}" defer></script>
    @endpush
@endonce
@endif
