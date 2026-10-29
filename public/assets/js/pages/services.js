/**
 * Services page choreography: hero tile wave, 3D flip-in service cards,
 * platform sheets sliding in from both sides, a sequenced process and
 * industry cards. Sections are marked [data-custom-anim] in the view.
 */
(function () {
    'use strict';

    if (!window.App || !window.App.motionOK) return;

    const st = (trigger, start = 'top 85%') => ({ trigger, start, once: true });

    /* Hero: tiles rise out of the grid from the centre, then keep a slow wave. */
    const tiles = document.querySelectorAll('.hero-tiles span');
    if (tiles.length) {
        gsap.from(tiles, {
            opacity: 0,
            scale: 0.4,
            duration: 0.9,
            ease: 'back.out(1.6)',
            stagger: { grid: [3, 4], from: 'center', amount: 0.6 },
            delay: 0.4,
        });
    }

    /* Service cards: flip up in 3D, then icons pop and check items slide in. */
    const grid = document.querySelector('[data-anim="flip-cards"]');
    if (grid) {
        const cards = grid.querySelectorAll('.service-card');
        // Hidden until their batch enters.
        gsap.set(cards, { opacity: 0, transformPerspective: 1100, transformOrigin: '50% 100%' });
        ScrollTrigger.batch(cards, {
            start: 'top 88%',
            once: true,
            onEnter: (batch) => {
                gsap.fromTo(batch,
                    { opacity: 0, y: 70, rotateX: -55 },
                    { opacity: 1, y: 0, rotateX: 0, duration: 1.1, ease: 'power3.out', stagger: 0.12, clearProps: 'transform' });
                batch.forEach((card, i) => {
                    const delay = 0.35 + i * 0.12;
                    gsap.from(card.querySelector('.icon-chip'), { scale: 0, rotate: -120, duration: 0.8, ease: 'back.out(2)', delay });
                    gsap.from(card.querySelectorAll('.check-list li'), { x: -16, opacity: 0, duration: 0.5, stagger: 0.07, delay: delay + 0.25 });
                });
            },
        });
    }

    /* Platform sheets: Meta from the left, Google from the right. */
    const sheets = document.querySelector('[data-anim="sheets"]');
    if (sheets) {
        sheets.querySelectorAll('.platform-sheet').forEach((sheet, i) => {
            const tl = gsap.timeline({ scrollTrigger: st(sheet) });
            tl.from(sheet, { x: i === 0 ? -80 : 80, opacity: 0, duration: 1, ease: 'power3.out' })
                .from(sheet.querySelector('.platform-sheet__head .icon-chip'), { scale: 0, rotate: 90, duration: 0.6, ease: 'back.out(2)' }, '-=0.6')
                .from(sheet.querySelectorAll('.platform-sheet__list li'), { y: 18, opacity: 0, duration: 0.5, stagger: 0.07 }, '-=0.4');
        });
    }

    /* Process: each step's number pops in, one after another. */
    const process = document.querySelector('[data-anim="process"]');
    if (process) {
        const steps = process.querySelectorAll('.process__step');
        const tl = gsap.timeline({ scrollTrigger: st(process, 'top 80%') });
        steps.forEach((step, i) => {
            tl.from(step.querySelector('.process__num'), { scale: 0, duration: 0.6, ease: 'back.out(2.2)' }, i * 0.22)
                .from(step.querySelectorAll('.process__title, .process__text'), { y: 20, opacity: 0, duration: 0.6, stagger: 0.08 }, i * 0.22 + 0.15);
        });
    }

    /* Industries: rise and scale, icons spin in. */
    const industries = document.querySelector('[data-anim="industries"]');
    if (industries) {
        const items = industries.querySelectorAll('.industry');
        const tl = gsap.timeline({ scrollTrigger: st(industries) });
        tl.from(items, { y: 60, scale: 0.92, opacity: 0, duration: 0.9, stagger: 0.1, ease: 'power3.out' })
            .from(industries.querySelectorAll('.industry .icon'), { rotate: -180, scale: 0, duration: 0.7, stagger: 0.1, ease: 'back.out(2)' }, 0.25);
    }
})();
