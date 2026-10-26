@props(['caseStudy', 'compact' => false])

@php
    $cs = $caseStudy;
    $group = 'case-'.$cs->id;
    $befores = $cs->beforeImages();
    $afters = $cs->afterImages();
    $metrics = $cs->metricRows();
    $categoryLabel = config('portfolio.categories.'.$cs->category, ucfirst($cs->category));
@endphp

<article {{ $attributes->merge(['class' => 'case-study'.($compact ? ' case-study--compact' : '')]) }}
    id="case-{{ $cs->slug }}"
    data-case-study
    data-platforms="{{ implode(' ', $cs->platforms ?? []) }}"
    data-category="{{ $cs->category }}">

    <header class="case-study__head">
        <div class="case-study__tags">
            @if ($cs->is_demo)
                <span class="demo-badge" title="Layout demonstration. Real screenshots and figures will replace this.">
                    <x-icon name="alert" size="14" /> Demonstration
                </span>
            @else
                <span class="verified-badge"><x-icon name="check" size="14" /> Real campaign data</span>
            @endif
            <span class="case-study__chip">{{ $categoryLabel }}</span>
            <x-platform-badges :platforms="$cs->platforms ?? []" />
        </div>
        <h3 class="case-study__title">{{ $cs->title }}</h3>
        <p class="case-study__meta">
            @if ($cs->brand)<span>{{ $cs->brand }}</span>@endif
            <span>{{ $cs->industry }}</span>
            <span><x-icon name="map-pin" size="14" /> {{ $cs->country }}</span>
            @if ($cs->period)<span>{{ $cs->period }}</span>@endif
        </p>
    </header>

    <div class="case-study__body">
        <div class="case-study__visual">
            @unless ($compact)
                <div class="view-toggle" role="group" aria-label="Comparison view">
                    <button type="button" class="view-toggle__btn is-active" data-view-btn="slider" aria-pressed="true"><x-icon name="slider" size="16" /> Slider</button>
                    <button type="button" class="view-toggle__btn" data-view-btn="split" aria-pressed="false"><x-icon name="columns" size="16" /> Side by side</button>
                </div>
            @endunless

            <div data-view="slider">
                <x-compare-slider :case-study="$cs" />
            </div>

            @unless ($compact)
                <div class="split-view" data-view="split" hidden>
                    @foreach (['before' => $befores->first(), 'after' => $afters->first()] as $stage => $image)
                        <figure class="split-view__pane split-view__pane--{{ $stage }}">
                            <figcaption class="split-view__label">{{ ucfirst($stage) }}</figcaption>
                            @if ($image)
                                <button type="button" class="split-view__open" data-lightbox-open="{{ $group }}" data-lightbox-index="{{ $stage === 'before' ? 0 : $befores->count() }}" aria-label="Open {{ $stage }} screenshot full size">
                                    <img src="{{ $image->url() }}" alt="{{ ucfirst($stage) }}: {{ $image->caption ?: $cs->title }}" loading="lazy" decoding="async">
                                </button>
                            @else
                                @include('partials.screenshot-placeholder', ['stage' => $stage])
                            @endif
                        </figure>
                    @endforeach
                </div>

                {{-- Every screenshot, grouped by stage; all feed the same lightbox. --}}
                @if ($befores->count() + $afters->count() > 0)
                    <div class="shot-strip">
                        @foreach (['before' => $befores, 'after' => $afters] as $stage => $images)
                            @if ($images->isNotEmpty())
                                <div class="shot-strip__group">
                                    <span class="shot-strip__label shot-strip__label--{{ $stage }}">{{ ucfirst($stage) }}</span>
                                    @foreach ($images as $image)
                                        <button type="button" class="shot-strip__thumb" data-lightbox-open="{{ $group }}"
                                            data-lightbox-index="{{ $stage === 'before' ? $loop->index : $befores->count() + $loop->index }}"
                                            aria-label="Open {{ $stage }} screenshot {{ $loop->iteration }}">
                                            <img src="{{ $image->url() }}" alt="" loading="lazy" decoding="async">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endunless

            {{-- Lightbox source list (before images first, then after). --}}
            <template data-lightbox-group="{{ $group }}">
                @foreach ($befores->concat($afters) as $image)
                    <a href="{{ $image->url() }}" data-stage="{{ $image->stage }}" data-caption="{{ $image->caption }}"></a>
                @endforeach
            </template>
        </div>

        <div class="case-study__info">
            <h4 class="case-study__subhead">What Ali did</h4>
            <p class="case-study__text">{{ $compact ? \Illuminate\Support\Str::limit($cs->strategy, 170) : $cs->strategy }}</p>

            @if (! $compact && $cs->improvements)
                <h4 class="case-study__subhead">What improved</h4>
                <p class="case-study__text">{{ $cs->improvements }}</p>
            @endif

            @if (! empty($metrics))
                <div class="cs-metrics">
                    @foreach ($compact ? array_slice($metrics, 0, 2) : $metrics as $m)
                        <div class="cs-metric">
                            <p class="cs-metric__label">{{ $m['label'] }}</p>
                            <p class="cs-metric__values">
                                @if (filled($m['before']))
                                    <span class="cs-metric__before">{{ $m['prefix'] }}{{ $m['before'] }}{{ $m['suffix'] }}</span>
                                    <x-icon name="arrow-right" size="14" />
                                @endif
                                <span class="cs-metric__after">{{ $m['prefix'] }}@if ($m['after_numeric'] !== null)<span data-cs-count="{{ $m['after'] }}">{{ $m['after'] }}</span>@else{{ $m['after'] }}@endif{{ $m['suffix'] }}</span>
                            </p>
                            @if ($m['change'] !== null)
                                <span @class(['cs-metric__change', 'is-good' => $m['improved'], 'is-bad' => $m['improved'] === false])>
                                    {{ $m['change'] > 0 ? '+' : '' }}{{ $m['change'] }}%
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif ($cs->is_demo)
                <p class="cs-metrics-note">
                    <x-icon name="chart" size="16" />
                    Verified before-and-after figures will appear here once real campaign screenshots are added.
                </p>
            @endif

            @if ($compact)
                <a class="case-study__more" href="{{ route('results') }}#case-{{ $cs->slug }}">Full case study <x-icon name="arrow-right" size="16" /></a>
            @endif
        </div>
    </div>
</article>
