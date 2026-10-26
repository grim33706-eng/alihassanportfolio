/**
 * Results page choreography: hero growth line with a travelling dot and pins,
 * a sequenced ROAS scale, staggered portfolio bars, comparison cards and the
 * market table. Sections are marked [data-custom-anim] in the view.
 */
(function () {
    'use strict';

    if (!window.App || !window.App.motionOK) return;

    const once = (trigger, start = 'top 85%') => ({ trigger, start, once: true });

    /* ---- Hero graph ---------------------------------------------------- */
    const graph = document.querySelector('[data-hero-graph]');
    if (graph) {
        const line = graph.querySelector('[data-graph-line]');
        const area = graph.querySelector('.hero-graph__area');
        const dot = graph.querySelector('[data-graph-dot]');
        const pins = graph.querySelectorAll('[data-graph-pin]');
        const length = line.getTotalLength();
        const VB_W = 600;
        const VB_H = 300;

        line.setAttribute('pathLength', '1');
        gsap.set(line, { strokeDasharray: 1, strokeDashoffset: 1 });
        gsap.set(area, { opacity: 0 });
        gsap.set(dot, { opacity: 0 });

        const progress = { p: 0 };
        const shown = [false, false];
        const placeDot = () => {
            const pt = line.getPointAtLength(progress.p * length);
            dot.style.left = `${(pt.x / VB_W) * 100}%`;
            dot.style.top = `${(pt.y / VB_H) * 100}%`;
            // Pop each pin as the dot passes it.
            [360, 598].forEach((x, i) => {
                if (!shown[i] && pt.x >= x) {
                    shown[i] = true;
                    pins[i].classList.add('is-shown');
                }
            });
        };

        const tl = gsap.timeline({ paused: true });
        tl.to(dot, { opacity: 1, duration: 0.3 }, 0)
            .to(progress, { p: 1, duration: 2.6, ease: 'power2.inOut', onUpdate: placeDot }, 0)
            .to(line, { strokeDashoffset: 0, duration: 2.6, ease: 'power2.inOut' }, 0)
            .to(area, { opacity: 1, duration: 1.6 }, 1)
            .to(dot, { scale: 1.35, duration: 0.9, ease: 'sine.inOut', yoyo: true, repeat: -1 }, 2.6);

        window.App.whenLoaded(() => tl.play());
    }

    /* ---- ROAS scale ------------------------------------------------------ */
    const roas = document.querySelector('[data-roas]');
    if (roas) {
        const tl = gsap.timeline({ scrollTrigger: once(roas) });
        tl.from(roas, { y: 50, opacity: 0, duration: 0.9, ease: 'power3.out' })
            .from(roas.querySelectorAll('.roas-chart__axis span'), { y: 8, opacity: 0, duration: 0.4, stagger: 0.05 }, '-=0.4')
            .from(roas.querySelector('.roas-chart__ref'), { scaleY: 0, duration: 0.5, ease: 'power2.out' }, '-=0.2')
            .to(roas.querySelector('[data-roas-bar]'), { scaleX: 1, duration: 1.3, ease: 'power3.inOut' }, '-=0.1')
            .fromTo(roas.querySelector('.roas-chart__band'),
                { clipPath: 'inset(0% 100% 0% 0%)' },
                { clipPath: 'inset(0% 0% 0% 0%)', duration: 0.8, ease: 'power2.out' }, '-=0.3')
            .from(roas.querySelectorAll('.roas-chart__label'), { y: 14, opacity: 0, duration: 0.5, stagger: 0.15, ease: 'back.out(2)' }, '-=0.6')
            .from(roas.querySelector('.chart-note'), { opacity: 0, duration: 0.6 }, '-=0.2');
    }

    /* ---- Portfolio-size bars -------------------------------------------- */
    const bars = document.querySelector('[data-bars]');
    if (bars) {
        const rows = bars.querySelectorAll('.bar-chart__row');
        const tl = gsap.timeline({ scrollTrigger: once(bars) });
        tl.from(bars, { y: 50, opacity: 0, duration: 0.9, ease: 'power3.out' })
            .from(rows, { x: -30, opacity: 0, duration: 0.6, stagger: 0.12 }, '-=0.4')
            .to(bars.querySelectorAll('[data-bar]'), { scaleX: 1, duration: 1.2, ease: 'power3.inOut', stagger: 0.12 }, '-=0.5');
    }

    /* ---- Comparison cards ----------------------------------------------- */
    const compare = document.querySelector('[data-compare-cards]');
    if (compare) {
        const cards = compare.querySelectorAll('.compare-card');
        const tl = gsap.timeline({ scrollTrigger: once(compare) });
        tl.from(cards, { x: -50, opacity: 0, duration: 0.8, stagger: 0.14, ease: 'power3.out' })
            .from(compare.querySelectorAll('.compare-card__row .icon'), { x: -20, opacity: 0, duration: 0.6, ease: 'power2.out' }, 0.4)
            .from(compare.querySelector('.compare-card__big'), { clipPath: 'inset(0% 100% 0% 0%)', duration: 1, ease: 'power3.inOut' }, 0.5);
    }

    /* ---- Market table --------------------------------------------------- */
    const table = document.querySelector('[data-market-table]');
    if (table) {
        const tl = gsap.timeline({ scrollTrigger: once(table) });
        tl.from(table, { y: 40, opacity: 0, duration: 0.8, ease: 'power3.out' })
            .from(table.querySelectorAll('thead th'), { opacity: 0, duration: 0.4, stagger: 0.06 }, '-=0.4')
            .from(table.querySelectorAll('tbody tr'), { y: 16, opacity: 0, duration: 0.45, stagger: 0.05 }, '-=0.2')
            .from(table.querySelectorAll('tbody .market-list__code'), { scale: 0, duration: 0.4, stagger: 0.05, ease: 'back.out(2.5)' }, '-=0.6');
    }
})();
