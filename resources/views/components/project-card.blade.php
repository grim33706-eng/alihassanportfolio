@props(['project'])

@php
    $markets = collect($project['markets'])->map(fn ($m) => \Illuminate\Support\Str::slug($m))->implode(' ');
    // Show at most three work items; the rest collapse into a "+N" chip so every card keeps the same rhythm.
    $work = collect($project['work']);
    $visibleWork = $work->take(3);
    $moreWork = $work->slice(3);
@endphp

<article {{ $attributes->merge(['class' => 'project-card']) }}
    id="project-{{ $project['slug'] }}"
    data-project
    data-platforms="{{ implode(' ', $project['platforms']) }}"
    data-category="{{ $project['category'] }}"
    data-markets="{{ $markets }}">
    <div class="project-card__art project-card__art--{{ $project['category'] }}" aria-hidden="true">
        <svg viewBox="0 0 320 120" preserveAspectRatio="none">
            <path class="project-card__area" d="M0 100 C 40 95, 60 80, 95 78 S 150 60, 185 52 S 250 30, 320 14 V120 H0 Z" />
            <path class="project-card__line" pathLength="1" d="M0 100 C 40 95, 60 80, 95 78 S 150 60, 185 52 S 250 30, 320 14" />
        </svg>
        <span class="project-card__category">{{ config('portfolio.categories.'.$project['category']) }}</span>
        <span class="project-card__illus">Illustrative</span>
    </div>

    <div class="project-card__body">
        <header class="project-card__head">
            <p class="project-card__meta">
                <x-icon name="map-pin" size="14" /> <span>{{ implode(' · ', $project['markets']) }}</span>
            </p>
            <h3 class="project-card__title">{{ $project['title'] }}</h3>
            <p class="project-card__industry">{{ $project['industry'] }}</p>
        </header>

        <dl class="project-card__facts">
            <div class="project-card__fact">
                <dt>Objective</dt>
                <dd class="project-card__objective">{{ $project['objective'] }}</dd>
            </div>
            <div class="project-card__fact">
                <dt>Work performed</dt>
                <dd>
                    <ul class="project-card__work" role="list">
                        @foreach ($visibleWork as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                        @if ($moreWork->isNotEmpty())
                            <li class="project-card__more" title="{{ $moreWork->implode(' · ') }}">
                                +{{ $moreWork->count() }} more
                                <span class="visually-hidden">: {{ $moreWork->implode(', ') }}</span>
                            </li>
                        @endif
                    </ul>
                </dd>
            </div>
        </dl>

        {{-- Every card gets the same highlight panel: the documented outcome when there is one,
             otherwise a scope summary built from the project's own data. --}}
        @if ($project['outcome'])
            <div class="project-card__panel project-card__panel--outcome">
                <p class="project-card__panel-label"><x-icon name="check" size="14" /> Documented outcome</p>
                <p class="project-card__panel-text">{{ $project['outcome'] }}</p>
            </div>
        @else
            <div class="project-card__panel project-card__panel--scope">
                <p class="project-card__panel-label"><x-icon name="layers" size="14" /> Campaign scope</p>
                <dl class="project-card__scope">
                    <div><dt>Markets</dt><dd>{{ count($project['markets']) }}</dd></div>
                    <div><dt>Platforms</dt><dd>{{ count($project['platforms']) }}</dd></div>
                    <div><dt>Work streams</dt><dd>{{ $work->count() }}</dd></div>
                </dl>
            </div>
        @endif

        <footer class="project-card__foot">
            <x-platform-badges :platforms="$project['platforms']" />
            <span class="project-card__period">{{ $project['period'] }}</span>
        </footer>
    </div>
</article>
