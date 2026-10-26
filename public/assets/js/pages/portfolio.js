/**
 * Portfolio: filter projects by platform, category and market.
 * Uses GSAP Flip for layout animation when motion is allowed.
 */
(function () {
    'use strict';

    const root = document.querySelector('[data-filters]');
    const grid = document.querySelector('[data-project-grid]');
    if (!root || !grid) return;

    const cards = Array.from(grid.querySelectorAll('[data-project]'));
    const visibleCount = root.querySelector('[data-filter-visible]');
    const resets = document.querySelectorAll('[data-filter-reset]');
    const ring = root.querySelector('[data-filter-ring]');
    const summary = root.querySelector('[data-filter-summary]');
    const empty = document.querySelector('[data-empty-state]');
    const select = root.querySelector('[data-filter-select="market"]');
    const motionOK = window.App && window.App.motionOK && typeof window.Flip !== 'undefined';
    const state = { platform: 'all', category: 'all', market: 'all' };

    if (motionOK) gsap.registerPlugin(Flip);

    const matches = (card) =>
        (state.platform === 'all' || card.dataset.platforms.split(' ').includes(state.platform)) &&
        (state.category === 'all' || card.dataset.category === state.category) &&
        (state.market === 'all' || card.dataset.markets.split(' ').includes(state.market));

    function apply() {
        const flipState = motionOK ? Flip.getState(cards) : null;
        let visible = 0;

        cards.forEach((card) => {
            const show = matches(card);
            card.classList.toggle('is-hidden', !show);
            if (show) visible++;
        });

        updateCount(visible);
        updateMeter(visible);
        empty.hidden = visible > 0;
        const filtered = state.platform !== 'all' || state.category !== 'all' || state.market !== 'all';
        resets.forEach((btn) => { btn.disabled = !filtered; });
        select.classList.toggle('is-set', state.market !== 'all');

        if (flipState) {
            Flip.from(flipState, {
                duration: 0.6,
                ease: 'power3.inOut',
                scale: true,
                absolute: true,
                onEnter: (els) => gsap.fromTo(els, { opacity: 0, scale: 0.92 }, { opacity: 1, scale: 1, duration: 0.5, stagger: 0.05 }),
                onLeave: (els) => gsap.to(els, { opacity: 0, scale: 0.92, duration: 0.35 }),
            });
        }
        // Layout changed: keep later scroll triggers accurate.
        if (window.ScrollTrigger) setTimeout(() => ScrollTrigger.refresh(), 650);
    }

    // The visible-projects number ticks to its new value rather than jumping.
    let shown = cards.length;
    // Ring fills to the share of projects shown; the line below names the active filters.
    function updateMeter(visible) {
        if (ring) ring.style.strokeDashoffset = String(100 - (visible / cards.length) * 100);
        if (!summary) return;
        const parts = ['platform', 'category']
            .filter((group) => state[group] !== 'all')
            .map((group) => root.querySelector(`[data-filter="${group}"].is-active`).dataset.label);
        if (state.market !== 'all') parts.push(select.options[select.selectedIndex].text);
        summary.textContent = parts.length ? parts.join(' · ') : 'All projects';
    }

    function updateCount(target) {
        if (!motionOK) {
            visibleCount.textContent = target;
            shown = target;
            return;
        }
        const state = { v: shown };
        gsap.to(state, { v: target, duration: 0.5, ease: 'power2.out', overwrite: true, onUpdate: () => { visibleCount.textContent = Math.round(state.v); } });
        shown = target;
        gsap.fromTo(visibleCount, { scale: 1.35, color: '#9db3ff' }, { scale: 1, color: '#F8FAFC', duration: 0.5, ease: 'back.out(2)' });
    }

    root.querySelectorAll('[data-filter]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const group = btn.dataset.filter;
            state[group] = btn.dataset.value;
            root.querySelectorAll(`[data-filter="${group}"]`).forEach((b) => {
                const active = b === btn;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', String(active));
            });
            apply();
        });
    });

    select.addEventListener('change', () => {
        state.market = select.value;
        apply();
    });

    resets.forEach((btn) => btn.addEventListener('click', () => {
        state.platform = state.category = state.market = 'all';
        select.value = 'all';
        root.querySelectorAll('[data-filter]').forEach((b) => {
            const active = b.dataset.value === 'all';
            b.classList.toggle('is-active', active);
            b.setAttribute('aria-pressed', String(active));
        });
        apply();
    }));

    // Edge fade on filter tracks that scroll (narrow screens).
    root.querySelectorAll('.pf-track').forEach((track) => {
        const update = () => {
            track.classList.toggle('is-scrollable', track.scrollWidth > track.clientWidth + 2);
            track.classList.toggle('is-end', track.scrollLeft + track.clientWidth >= track.scrollWidth - 2);
        };
        track.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
        update();
    });

    // Entrance.
    if (window.App && window.App.motionOK) {
        // Hero: the stacked cards start squared-up and fan out into place.
        const stack = document.querySelectorAll('.hero-cards span');
        if (stack.length) {
            gsap.from(stack, { rotate: 0, x: 0, y: 40, opacity: 0, duration: 1.3, ease: 'power4.out', stagger: 0.12, delay: 0.5 });
            gsap.to(stack[2], { y: -10, rotate: 7, duration: 3.5, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 2 });
        }

        // Movement only (no fade-from-invisible), so the filters can never be left hidden.
        gsap.from(root, { y: -18, duration: 0.8, delay: 0.3, clearProps: 'transform' });
        gsap.from(root.querySelectorAll('.pf-track, .pf-select-wrap'), { x: -14, duration: 0.6, stagger: 0.08, delay: 0.45, clearProps: 'transform' });

        // Cards wipe up from the bottom edge as they scroll in.
        // Rise + fade (GPU-composited) rather than a clip-path wipe, which repaints every frame.
        gsap.set(cards, { y: 48, autoAlpha: 0 });
        ScrollTrigger.batch(cards, {
            start: 'top 92%',
            once: true,
            onEnter: (batch) => {
                gsap.to(batch, { y: 0, autoAlpha: 1, duration: 0.9, ease: 'power3.out', stagger: 0.08, clearProps: 'transform,opacity,visibility' });
                batch.forEach((card, i) => {
                    gsap.from(card.querySelectorAll('.project-card__fact, .project-card__panel, .project-card__foot'), { y: 16, opacity: 0, duration: 0.6, stagger: 0.08, delay: 0.35 + i * 0.1 });
                });
            },
        });
    }
})();
