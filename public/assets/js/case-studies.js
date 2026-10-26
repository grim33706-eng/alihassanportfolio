/**
 * Before & after case studies: comparison sliders, slider / side-by-side
 * toggle, full-size lightbox, filters, metric counters and reveal motion.
 */
(function () {
    'use strict';

    const motionOK = window.App && window.App.motionOK;
    const sections = document.querySelectorAll('[data-case-studies]');
    if (!sections.length) return;

    /* ------------------------------------------------------------------
       Comparison sliders
    ------------------------------------------------------------------ */
    function setPos(compare, value) {
        const v = Math.min(100, Math.max(0, value));
        compare.style.setProperty('--pos', `${v}%`);
        compare.dataset.near = v < 15 ? 'after' : v > 85 ? 'before' : '';
        const range = compare.querySelector('[data-compare-range]');
        range.value = v;
        range.setAttribute('aria-valuetext', `${Math.round(v)}% before, ${Math.round(100 - v)}% after`);
    }

    document.querySelectorAll('[data-compare]').forEach((compare) => {
        const range = compare.querySelector('[data-compare-range]');
        range.addEventListener('input', () => setPos(compare, parseFloat(range.value)));
        range.addEventListener('pointerdown', () => compare.classList.add('is-dragging'));
        window.addEventListener('pointerup', () => compare.classList.remove('is-dragging'));

        // Intro sweep: start fully on "before", then glide to the middle.
        if (motionOK) {
            const state = { v: 100 };
            setPos(compare, 100);
            gsap.to(state, {
                v: 50,
                duration: 1.6,
                ease: 'power3.inOut',
                scrollTrigger: { trigger: compare, start: 'top 80%', once: true },
                onUpdate: () => setPos(compare, state.v),
            });
        }
    });

    /* ------------------------------------------------------------------
       Slider / side-by-side toggle
    ------------------------------------------------------------------ */
    document.querySelectorAll('[data-case-study]').forEach((card) => {
        const buttons = card.querySelectorAll('[data-view-btn]');
        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const view = btn.dataset.viewBtn;
                buttons.forEach((b) => {
                    const on = b === btn;
                    b.classList.toggle('is-active', on);
                    b.setAttribute('aria-pressed', String(on));
                });
                card.querySelectorAll('[data-view]').forEach((pane) => {
                    const show = pane.dataset.view === view;
                    pane.hidden = !show;
                    if (show && motionOK) {
                        gsap.fromTo(pane.children, { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.5, stagger: 0.08, clearProps: 'transform' });
                    }
                });
            });
        });
    });

    /* ------------------------------------------------------------------
       Lightbox (native <dialog>: focus trap, Esc and focus return built in)
    ------------------------------------------------------------------ */
    let dialog = null;
    let items = [];
    let index = 0;

    function buildDialog() {
        dialog = document.createElement('dialog');
        dialog.className = 'lightbox';
        dialog.setAttribute('aria-label', 'Screenshot viewer');
        dialog.innerHTML = `
            <div class="lightbox__inner">
                <div class="lightbox__bar">
                    <div><span class="lightbox__stage" data-lb-stage></span><span class="lightbox__count" data-lb-count></span></div>
                    <button type="button" class="lightbox__btn" data-lb-close aria-label="Close viewer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </div>
                <figure class="lightbox__figure"><img alt="" data-lb-img></figure>
                <p class="lightbox__caption" data-lb-caption></p>
                <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--prev" data-lb-prev aria-label="Previous screenshot">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
                </button>
                <button type="button" class="lightbox__btn lightbox__nav lightbox__nav--next" data-lb-next aria-label="Next screenshot">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                </button>
            </div>`;
        document.body.appendChild(dialog);

        dialog.querySelector('[data-lb-close]').addEventListener('click', () => dialog.close());
        dialog.querySelector('[data-lb-prev]').addEventListener('click', () => show(index - 1));
        dialog.querySelector('[data-lb-next]').addEventListener('click', () => show(index + 1));
        // Click on the dimmed backdrop closes.
        dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });
        dialog.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') show(index - 1);
            if (e.key === 'ArrowRight') show(index + 1);
        });
    }

    function show(i) {
        if (!items.length) return;
        index = (i + items.length) % items.length;
        const item = items[index];
        const img = dialog.querySelector('[data-lb-img]');
        const stage = dialog.querySelector('[data-lb-stage]');
        img.src = item.src;
        img.alt = `${item.stage === 'after' ? 'After' : 'Before'} screenshot${item.caption ? `: ${item.caption}` : ''}`;
        stage.textContent = item.stage === 'after' ? 'After' : 'Before';
        stage.dataset.stage = item.stage;
        dialog.querySelector('[data-lb-count]').textContent = `${index + 1} / ${items.length}`;
        dialog.querySelector('[data-lb-caption]').textContent = item.caption || '';
        const multiple = items.length > 1;
        dialog.querySelector('[data-lb-prev]').hidden = !multiple;
        dialog.querySelector('[data-lb-next]').hidden = !multiple;
        if (motionOK) gsap.fromTo(img, { opacity: 0, scale: 0.97 }, { opacity: 1, scale: 1, duration: 0.35 });
    }

    document.addEventListener('click', (e) => {
        const opener = e.target.closest('[data-lightbox-open]');
        if (!opener) return;
        const template = document.querySelector(`template[data-lightbox-group="${opener.dataset.lightboxOpen}"]`);
        if (!template) return;
        items = Array.from(template.content.querySelectorAll('a')).map((a) => ({
            src: a.getAttribute('href'),
            stage: a.dataset.stage,
            caption: a.dataset.caption,
        }));
        if (!items.length) return;
        if (!dialog) buildDialog();
        show(parseInt(opener.dataset.lightboxIndex || '0', 10));
        dialog.showModal();
    });

    /* ------------------------------------------------------------------
       Filters (Results page)
    ------------------------------------------------------------------ */
    const filterRoot = document.querySelector('[data-cs-filters]');
    if (filterRoot) {
        const cards = Array.from(document.querySelectorAll('#before-after [data-case-study]'));
        const count = filterRoot.querySelector('[data-cs-count]');
        const empty = document.querySelector('[data-cs-empty]');
        const state = { platform: 'all', category: 'all' };

        filterRoot.querySelectorAll('[data-cs-filter]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const group = btn.dataset.csFilter;
                state[group] = btn.dataset.value;
                filterRoot.querySelectorAll(`[data-cs-filter="${group}"]`).forEach((b) => {
                    const on = b === btn;
                    b.classList.toggle('is-active', on);
                    b.setAttribute('aria-pressed', String(on));
                });

                let visible = 0;
                cards.forEach((card) => {
                    const match = (state.platform === 'all' || card.dataset.platforms.split(' ').includes(state.platform))
                        && (state.category === 'all' || card.dataset.category === state.category);
                    card.classList.toggle('is-hidden', !match);
                    if (match) {
                        visible++;
                        if (motionOK) gsap.fromTo(card, { opacity: 0, y: 24 }, { opacity: 1, y: 0, duration: 0.5, clearProps: 'transform' });
                    }
                });
                count.textContent = `Showing ${visible} of ${cards.length}`;
                empty.hidden = visible > 0;
                if (window.ScrollTrigger) ScrollTrigger.refresh();
            });
        });
    }

    /* ------------------------------------------------------------------
       Metric counters (decimal aware)
    ------------------------------------------------------------------ */
    document.querySelectorAll('[data-cs-count]').forEach((el) => {
        if (!motionOK) return;
        const target = parseFloat(el.dataset.csCount);
        const decimals = (el.dataset.csCount.split('.')[1] || '').length;
        const state = { v: 0 };
        gsap.to(state, {
            v: target,
            duration: 1.8,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 92%', once: true },
            onUpdate: () => { el.textContent = state.v.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals }); },
        });
    });

    /* ------------------------------------------------------------------
       Reveal choreography
    ------------------------------------------------------------------ */
    if (motionOK) {
        document.querySelectorAll('[data-case-study]').forEach((card) => {
            const tl = gsap.timeline({ scrollTrigger: { trigger: card, start: 'top 85%', once: true } });
            tl.from(card, { y: 60, opacity: 0, duration: 1, ease: 'power3.out', clearProps: 'transform' })
                .from(card.querySelectorAll('.case-study__tags > *'), { y: 10, opacity: 0, duration: 0.4, stagger: 0.05 }, '-=0.6')
                .from(card.querySelector('.compare'), { autoAlpha: 0, scale: 0.96, duration: 0.9, ease: 'power3.out', clearProps: 'transform,opacity,visibility' }, '-=0.7')
                .from(card.querySelectorAll('.case-study__info > *'), { x: 24, opacity: 0, duration: 0.6, stagger: 0.08 }, '-=0.8');
        });
    }
})();
