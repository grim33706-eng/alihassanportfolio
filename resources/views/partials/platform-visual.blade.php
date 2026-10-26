{{-- Decorative, clearly illustrative visuals for the platform tabs. No real data. --}}
@if ($key === 'meta')
    <div class="pv pv--meta" aria-hidden="true" data-panel-item>
        <div class="pv-rings">
            <span class="pv-ring pv-ring--1"></span>
            <span class="pv-ring pv-ring--2"></span>
            <span class="pv-ring pv-ring--3"></span>
            <span class="pv-core"><x-icon name="meta" size="26" /></span>
            <span class="pv-node" style="--a: 20deg; --r: 46%"></span>
            <span class="pv-node" style="--a: 140deg; --r: 46%"></span>
            <span class="pv-node" style="--a: 260deg; --r: 46%"></span>
            <span class="pv-node pv-node--sm" style="--a: 80deg; --r: 31%"></span>
            <span class="pv-node pv-node--sm" style="--a: 200deg; --r: 31%"></span>
            <span class="pv-node pv-node--sm" style="--a: 320deg; --r: 31%"></span>
        </div>
        <ul class="pv-legend">
            <li><span style="--c: var(--accent)"></span>Prospecting</li>
            <li><span style="--c: var(--violet)"></span>Remarketing</li>
            <li><span style="--c: var(--teal)"></span>Audience segments</li>
        </ul>
        <span class="pv-caption">Illustrative</span>
    </div>
@else
    <div class="pv pv--google" aria-hidden="true" data-panel-item>
        <div class="pv-search">
            <x-icon name="search" size="18" />
            <span class="pv-search__text" data-typing="buy luxury bedding set|best cotton duvet cover|king size sheets online">buy luxury bedding set</span>
            <span class="pv-search__caret"></span>
        </div>
        <ul class="pv-keywords">
            <li>[exact match]</li>
            <li>"phrase match"</li>
            <li>negative keywords</li>
            <li>PMax asset groups</li>
        </ul>
        <div class="pv-bars">
            <span style="--h: 38%"></span><span style="--h: 52%"></span><span style="--h: 47%"></span><span style="--h: 66%"></span><span style="--h: 72%"></span><span style="--h: 88%"></span>
        </div>
        <span class="pv-caption">Illustrative</span>
    </div>
@endif
