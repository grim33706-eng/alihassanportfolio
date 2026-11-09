{{--
    Meta Ads results from Ads Manager reports (config: portfolio.meta_results, edited under
    Content > Meta Ads results). ROAS and cost-per figures are worked out from the stored
    totals, so they always agree with them.
--}}
@php
    $m = config('portfolio.meta_results');
    $a = $m['account'] ?? [];
    $campaigns = collect($m['campaigns'] ?? []);
    $months = collect($m['months'] ?? []);

    $ratio = fn ($top, $bottom) => (float) $bottom > 0 ? (float) $top / (float) $bottom : 0;
    $rs = fn ($n, $decimals = 0) => 'Rs'.number_format((float) $n, $decimals);
    $x = fn ($v) => number_format($v, 2);

    $roas = $ratio($a['value'] ?? 0, $a['spend'] ?? 0);
    $campaignRoas = $campaigns->map(fn ($c) => $ratio($c['value'] ?? 0, $c['spend'] ?? 0));
    $topRoas = max(1, (float) $campaignRoas->max());
    $bestRoas = $campaignRoas->sortDesc()->keys()->first();
    $bestValue = $campaigns->sortByDesc(fn ($c) => (float) ($c['value'] ?? 0))->keys()->first();
@endphp

@if ($roas > 0)
<section class="section" id="meta-results" data-meta-results data-custom-anim>
    <div class="container-xl">
        <x-section-heading :eyebrow="txt($m['eyebrow'])" :title="rich($m['title'])" :lead="txt($m['lead'])" />

        {{-- Overall report --}}
        <div class="meta-report" data-meta-report>
            <span class="meta-report__orb" aria-hidden="true"></span>
            <div class="meta-report__main">
                <p class="meta-chip"><x-icon name="meta" size="16" /> {{ txt($a['period']) }}</p>
                <p class="meta-report__roas"><span data-count="{{ $x($roas) }}" data-decimals="2">{{ $x($roas) }}</span>x</p>
                <p class="meta-report__label">Return on ad spend</p>
                <p class="meta-report__sentence">Every Rs1 spent returned <strong>{{ $rs($roas, 2) }}</strong> in tracked purchases.</p>

                <div class="meta-flow">
                    <div class="meta-flow__row">
                        <div class="meta-flow__text"><span>Purchase value</span><strong>Rs<span data-count="{{ round($a['value']) }}">{{ number_format($a['value']) }}</span></strong></div>
                        <span class="meta-flow__track"><span class="meta-flow__bar meta-flow__bar--value" style="--w: 100%" data-meta-bar></span></span>
                    </div>
                    <div class="meta-flow__row">
                        <div class="meta-flow__text"><span>Amount spent</span><strong>Rs<span data-count="{{ round($a['spend']) }}">{{ number_format($a['spend']) }}</span></strong></div>
                        <span class="meta-flow__track"><span class="meta-flow__bar" style="--w: {{ round($ratio($a['spend'], $a['value']) * 100, 2) }}%" data-meta-bar></span></span>
                    </div>
                </div>
            </div>

            <div class="meta-funnel" aria-label="Campaign funnel">
                <p class="meta-funnel__title">From reach to intent</p>
                @foreach ([
                    ['Accounts reached', $a['reach'], null, 100],
                    ['Content views', $a['views'], $rs($ratio($a['spend'], $a['views']), 2).' per view', 76],
                    ['Adds to cart', $a['atc'], $rs($ratio($a['spend'], $a['atc']), 2).' per add', 52],
                ] as [$label, $number, $cost, $width])
                    <div class="meta-funnel__step">
                        <span class="meta-funnel__bar" style="--w: {{ $width }}%" data-meta-funnel></span>
                        <span class="meta-funnel__label">{{ $label }}@if ($cost)<small>{{ $cost }}</small>@endif</span>
                        <strong class="meta-funnel__value" data-count="{{ $number }}">{{ number_format($number) }}</strong>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Largest campaigns --}}
        @if ($campaigns->isNotEmpty())
            <div class="meta-campaigns" data-meta-campaigns>
                <div class="meta-sub">
                    <h3 class="meta-sub__title">{{ txt($m['campaigns_title']) }}</h3>
                    <p class="meta-sub__text">{{ txt($m['campaigns_note']) }}</p>
                </div>
                <div class="meta-table-wrap">
                    <table class="meta-table">
                        <caption class="visually-hidden">{{ txt($m['campaigns_title']) }}</caption>
                        <thead>
                            <tr><th scope="col">Campaign</th><th scope="col">ROAS</th><th scope="col">Purchase value</th><th scope="col">Spent</th><th scope="col">Purchases</th><th scope="col">Cost per purchase</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($campaigns as $i => $c)
                                @php
                                    $r = $campaignRoas[$i];
                                @endphp
                                <tr class="meta-table__row">
                                    <th scope="row">
                                        <div class="meta-table__who"><span class="meta-table__rank">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="meta-table__name">{{ txt($c['label']) }}
                                            @if ($i === $bestRoas)<em class="meta-tag">Best ROAS</em>@endif
                                            @if ($i === $bestValue)<em class="meta-tag meta-tag--teal">Highest revenue</em>@endif
                                        </span></div>
                                    </th>
                                    <td data-label="ROAS" class="meta-table__roas">
                                        <strong>{{ $x($r) }}x</strong>
                                        <span class="meta-table__track"><span style="--w: {{ round($r / $topRoas * 100, 1) }}%" data-meta-roas></span></span>
                                    </td>
                                    <td data-label="Purchase value">{{ $rs($c['value']) }}</td>
                                    <td data-label="Spent">{{ $rs($c['spend']) }}</td>
                                    <td data-label="Purchases">{{ number_format($c['purchases']) }}</td>
                                    <td data-label="Cost per purchase">{{ $rs($ratio($c['spend'], $c['purchases'])) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Single months --}}
        @if ($months->isNotEmpty())
            <div class="meta-sub meta-sub--months">
                <h3 class="meta-sub__title">{{ txt($m['months_title']) }}</h3>
            </div>
            <div class="meta-months" data-meta-months>
                @foreach ($months as $month)
                    @php
                        $r = $ratio($month['value'] ?? 0, $month['spend'] ?? 0);
                    @endphp
                    <article class="meta-month" data-tilt>
                        <header class="meta-month__head">
                            <div>
                                <h4 class="meta-month__title">{{ txt($month['label']) }}</h4>
                                <p class="meta-month__note">{{ txt($month['note'] ?? '') }}</p>
                            </div>
                            <div class="meta-ring">
                                <svg viewBox="0 0 80 80" aria-hidden="true">
                                    <circle class="meta-ring__bg" cx="40" cy="40" r="34" pathLength="100" />
                                    <circle class="meta-ring__fill" cx="40" cy="40" r="34" pathLength="100" stroke-dasharray="{{ round(min($r / 10, 1) * 100, 1) }} 100" data-meta-ring />
                                </svg>
                                <span class="meta-ring__value">{{ $x($r) }}x<small>ROAS</small></span>
                            </div>
                        </header>
                        <dl class="meta-month__stats">
                            <div><dt>Purchase value</dt><dd>{{ $rs($month['value']) }}</dd></div>
                            <div><dt>Spent</dt><dd>{{ $rs($month['spend']) }}</dd></div>
                            <div><dt>Purchases</dt><dd>{{ number_format($month['purchases']) }}</dd></div>
                            <div><dt>Cost per purchase</dt><dd>{{ $rs($ratio($month['spend'], $month['purchases'])) }}</dd></div>
                            <div><dt>Content views</dt><dd>{{ number_format($month['views']) }}</dd></div>
                            <div><dt>Adds to cart</dt><dd>{{ number_format($month['atc']) }}</dd></div>
                        </dl>
                    </article>
                @endforeach
            </div>
        @endif

        <p class="chart-note meta-note">{{ txt($m['note']) }}</p>
    </div>
</section>
@endif
