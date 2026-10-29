/**
 * Shared site behaviour: navigation, motion system, reveals, counters,
 * tilt cards, chart tooltips and page transitions.
 *
 * Content is always visible without JavaScript. Motion is only applied when
 * GSAP has loaded and the visitor has not asked for reduced motion.
 */
(function () {
    'use strict';

    const root = document.documentElement;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const hasGsap = typeof window.gsap !== 'undefined' && typeof window.ScrollTrigger !== 'undefined';
    const motionOK = hasGsap && !reducedMotion.matches;
    const teardownFns = [];
    let lenis = null;

    if (motionOK) {
        root.classList.add('motion-ok');
        gsap.registerPlugin(ScrollTrigger);
        // Don't recalculate every trigger when a mobile address bar shows/hides.
        ScrollTrigger.config({ ignoreMobileResize: true });
        gsap.defaults({ ease: 'power3.out', duration: 0.9 });
    }

    // Small public API for page scripts and 3D modules.
    window.App = {
        motionOK,
        reducedMotion: reducedMotion.matches,
        finePointer: finePointer.matches,
        onTeardown(fn) { teardownFns.push(fn); },
        whenLoaded: (fn) => whenLoaded(fn),
        splitWords,
    };

    /* ------------------------------------------------------------------
       Split headings into words for masked reveals
    ------------------------------------------------------------------ */
    function splitWords(el) {
        if (el.dataset.splitDone) return el.querySelectorAll('.split-word__inner');
        el.dataset.splitDone = '1';

        const makeWord = (text, wrapper) => {
            const outer = document.createElement('span');
            outer.className = 'split-word';
            const inner = document.createElement('span');
            inner.className = 'split-word__inner';
            if (wrapper) {
                const clone = wrapper.cloneNode(false);
                clone.textContent = text;
                inner.appendChild(clone);
            } else {
                inner.textContent = text;
            }
            outer.appendChild(inner);
            return outer;
        };

        const frag = document.createDocumentFragment();
        Array.from(el.childNodes).forEach((node) => {
            const isText = node.nodeType === Node.TEXT_NODE;
            if (!isText && node.nodeType !== Node.ELEMENT_NODE) return;
            if (!isText && node.tagName === 'BR') { frag.appendChild(node.cloneNode()); return; }

            const parts = node.textContent.split(/( +)/);
            parts.forEach((part) => {
                if (part === '') return;
                if (/^ +$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
                frag.appendChild(makeWord(part, isText ? null : node));
            });
        });
        el.textContent = '';
        el.appendChild(frag);
        return el.querySelectorAll('.split-word__inner');
    }

    /* ------------------------------------------------------------------
       Header: scrolled state + hide on scroll down
    ------------------------------------------------------------------ */
    const header = document.querySelector('[data-header]');
    const progress = document.querySelector('[data-scroll-progress]');
    let lastY = window.scrollY;
    let ticking = false;
    // Page height is measured only when it can change, never inside the scroll handler
    // (reading it there forces a full layout on every scroll frame).
    let maxScroll = 0;
    const measure = () => { maxScroll = document.documentElement.scrollHeight - window.innerHeight; };
    measure();
    if ('ResizeObserver' in window) new ResizeObserver(measure).observe(document.body);

    function onScroll() {
        const y = window.scrollY;
        const max = maxScroll;
        progress.style.transform = `scaleX(${max > 0 ? Math.min(y / max, 1) : 0})`;

        header.classList.toggle('is-scrolled', y > 20);
        if (!header.classList.contains('menu-open')) {
            header.classList.toggle('is-hidden', y > 400 && y > lastY + 4);
            if (y < lastY - 4) header.classList.remove('is-hidden');
        }
        lastY = y;
        ticking = false;
    }
    window.addEventListener('scroll', () => {
        if (!ticking) { requestAnimationFrame(onScroll); ticking = true; }
    }, { passive: true });
    window.addEventListener('resize', () => { measure(); onScroll(); });
    onScroll();

    // Reveal the header again when keyboard focus lands inside it.
    header.addEventListener('focusin', () => header.classList.remove('is-hidden'));

    /* ------------------------------------------------------------------
       Mobile menu
    ------------------------------------------------------------------ */
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    const menuLabel = toggle.querySelector('[data-menu-label]');

    function setMenu(open) {
        toggle.setAttribute('aria-expanded', String(open));
        menuLabel.textContent = open ? 'Close menu' : 'Open menu';
        header.classList.toggle('menu-open', open);
        document.body.classList.toggle('no-scroll', open);
        // Smooth scroll must pause too, or the page keeps moving under the menu.
        if (lenis) open ? lenis.stop() : lenis.start();

        if (open) {
            menu.hidden = false;
            if (motionOK) {
                gsap.fromTo(menu, { clipPath: 'inset(0 0 100% 0)' }, { clipPath: 'inset(0 0 0% 0)', duration: 0.6, ease: 'power4.inOut' });
                gsap.fromTo(menu.querySelectorAll('.mobile-menu__link, .mobile-menu__foot'),
                    { y: 40, opacity: 0 }, { y: 0, opacity: 1, stagger: 0.05, delay: 0.2, duration: 0.6 });
            }
            menu.querySelector('a').focus({ preventScroll: true });
        } else if (motionOK && !menu.hidden) {
            gsap.to(menu, { clipPath: 'inset(0 0 100% 0)', duration: 0.45, ease: 'power3.inOut', onComplete: () => { menu.hidden = true; } });
        } else {
            menu.hidden = true;
        }
    }

    toggle.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setMenu(false);
            toggle.focus();
        }
    });
    window.matchMedia('(min-width: 1200px)').addEventListener('change', (e) => { if (e.matches) setMenu(false); });

    /* ------------------------------------------------------------------
       Hero entrance
    ------------------------------------------------------------------ */
    function heroIntro() {
        const hero = document.querySelector('[data-page-hero]');
        if (!hero) return null;
        const title = hero.querySelector('[data-split="hero"]');
        const words = title ? splitWords(title) : [];
        const items = hero.querySelectorAll('[data-hero-item]');
        const chips = hero.querySelectorAll('[data-hero-chip]');

        // Paused until the page loader has lifted, so the intro is seen in full.
        const tl = gsap.timeline({ paused: true, delay: 0.05 });
        tl.from(items[0] || [], { y: 16, opacity: 0, duration: 0.6 })
            .from(words, { yPercent: 110, duration: 1, stagger: 0.045, ease: 'power4.out' }, '-=0.3')
            .from(Array.from(items).slice(1), { y: 24, opacity: 0, stagger: 0.1 }, '-=0.6');
        if (chips.length) {
            tl.from(chips, { y: 30, opacity: 0, scale: 0.94, stagger: 0.12, duration: 0.8 }, '-=0.7');
            // Gentle float, offset per chip.
            chips.forEach((chip, i) => {
                gsap.to(chip, { y: i % 2 ? 8 : -8, duration: 3 + i * 0.6, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 1.5 });
            });
        }

        // Parallax: hero copy drifts and fades as you scroll away.
        const inner = hero.querySelector('.hero__inner, .page-hero__inner');
        if (inner) {
            gsap.to(inner, {
                yPercent: 12,
                opacity: 0.2,
                ease: 'none',
                scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true },
            });
        }
        const visual = hero.querySelector('.page-hero__visual');
        if (visual) {
            // Fade and ease back rather than translate, so the art never slides out of frame.
            tl.from(visual, { opacity: 0, scale: 0.94, duration: 1.4 }, 0.2);
            gsap.fromTo(visual, { opacity: 1, scale: 1 }, {
                opacity: 0.25,
                scale: 0.96,
                ease: 'none',
                immediateRender: false,
                scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true },
            });

            // Depth: the art drifts against the pointer.
            if (finePointer.matches) {
                const qx = gsap.quickTo(visual, 'x', { duration: 1.2, ease: 'power3.out' });
                const qy = gsap.quickTo(visual, 'y', { duration: 1.2, ease: 'power3.out' });
                hero.addEventListener('pointermove', (e) => {
                    qx((e.clientX / window.innerWidth - 0.5) * -36);
                    qy((e.clientY / window.innerHeight - 0.5) * -24);
                });
                hero.addEventListener('pointerleave', () => { qx(0); qy(0); });
            }
        }

        return tl;
    }

    /* ------------------------------------------------------------------
       Scroll reveals
    ------------------------------------------------------------------ */
    function scrollReveals() {
        document.querySelectorAll('[data-split]:not([data-split="hero"])').forEach((el) => {
            const words = splitWords(el);
            gsap.from(words, {
                yPercent: 110,
                duration: 0.9,
                stagger: 0.035,
                ease: 'power4.out',
                scrollTrigger: { trigger: el, start: 'top 88%', once: true },
            });
        });

        // Sections marked [data-custom-anim] are choreographed by their page script.
        const generic = (selector) => gsap.utils.toArray(selector).filter((el) => !el.closest('[data-custom-anim]'));

        generic('[data-reveal]').forEach((el) => {
            gsap.from(el, { y: 28, opacity: 0, scrollTrigger: { trigger: el, start: 'top 90%', once: true } });
        });

        const cards = generic('[data-reveal-card]');
        if (cards.length) {
            gsap.set(cards, { y: 40, opacity: 0 });
            ScrollTrigger.batch(cards, {
                start: 'top 92%',
                once: true,
                onEnter: (batch) => gsap.to(batch, { y: 0, opacity: 1, stagger: 0.09, duration: 0.9, overwrite: true, clearProps: 'transform' }),
            });
        }

        // Project-card sparklines draw themselves in as cards appear.
        gsap.utils.toArray('.project-card__line').forEach((line) => {
            const card = line.closest('.project-card');
            gsap.fromTo(line, { strokeDashoffset: 1 }, { strokeDashoffset: 0, duration: 1.6, ease: 'power2.inOut', scrollTrigger: { trigger: card, start: 'top 90%', once: true } });
            gsap.from(card.querySelector('.project-card__area'), { opacity: 0, duration: 1.2, delay: 0.5, scrollTrigger: { trigger: card, start: 'top 90%', once: true } });
        });

        // Process line fills as the steps scroll through.
        // Process steps light up in turn as they scroll into view. On desktop Services
        // the 3D pipeline (process-scene.js) does this instead, in step with the pipe.
        const pipeline = window.matchMedia('(min-width: 992px)');
        document.querySelectorAll('[data-process]').forEach((el) => {
            if (pipeline.matches && el.parentElement.querySelector('[data-process-scene]')) return;
            el.querySelectorAll('.process__step').forEach((step, i) => {
                ScrollTrigger.create({
                    trigger: step,
                    start: 'top 82%',
                    once: true,
                    onEnter: () => gsap.delayedCall(0.25 + i * 0.22, () => step.classList.add('is-active')),
                });
            });
        });

        // Timeline rail + items.
        document.querySelectorAll('[data-timeline]').forEach((el) => {
            const fill = el.querySelector('[data-timeline-fill]');
            if (fill) gsap.to(fill, { scaleY: 1, ease: 'none', scrollTrigger: { trigger: el, start: 'top 70%', end: 'bottom 70%', scrub: 0.6 } });
            el.querySelectorAll('[data-timeline-item]').forEach((item) => {
                gsap.from(item, { x: 40, opacity: 0, scrollTrigger: { trigger: item, start: 'top 85%', once: true } });
            });
        });

        // Charts.
        generic('[data-roas-bar], [data-bar]').forEach((bar, i) => {
            gsap.to(bar, { scaleX: 1, duration: 1.4, ease: 'power3.inOut', delay: (i % 4) * 0.08, scrollTrigger: { trigger: bar, start: 'top 90%', once: true } });
        });
    }

    /* ------------------------------------------------------------------
       Animated counters
    ------------------------------------------------------------------ */
    function counters() {
        document.querySelectorAll('[data-count]').forEach((el) => {
            const target = parseFloat(el.dataset.count);
            const state = { v: 0 };
            el.textContent = '0';
            gsap.to(state, {
                v: target,
                duration: 1.8,
                ease: 'power2.out',
                scrollTrigger: { trigger: el, start: 'top 92%', once: true },
                onUpdate: () => { el.textContent = Math.round(state.v); },
            });
        });
    }

    /* ------------------------------------------------------------------
       Tilt + cursor glow on cards (fine pointers only)
    ------------------------------------------------------------------ */
    function tiltCards() {
        document.querySelectorAll('[data-tilt]').forEach((card) => {
            // Measure once per hover and reuse one tween per axis, instead of a layout
            // read and a new tween on every pointer move. The tweens are made fresh on each
            // hover: the leave reset (or a card's entrance animation) may have killed the last ones.
            let rect = null;
            let rotX = null;
            let rotY = null;
            let layers = [];

            // Depth: the icon floats furthest above the card, the title a little less.
            const depthEls = [
                [card.querySelector('.icon-chip, [class*="__icon"]'), 14],
                [card.querySelector('h3'), 7],
            ].filter(([el]) => el);

            // A light sheen that follows the pointer (styled in app.css).
            if (getComputedStyle(card).position === 'static') card.style.position = 'relative';
            const sheen = document.createElement('span');
            sheen.className = 'tilt-sheen';
            sheen.setAttribute('aria-hidden', 'true');
            card.appendChild(sheen);

            // Start tilting only once the card's own animations (entrance, leave reset) are
            // done, so they don't fight over the same transform.
            const arm = () => {
                rotX = gsap.quickTo(card, 'rotationX', { duration: 0.6, ease: 'power2.out' });
                rotY = gsap.quickTo(card, 'rotationY', { duration: 0.6, ease: 'power2.out' });
                layers = depthEls.map(([el, depth]) => ({
                    depth,
                    x: gsap.quickTo(el, 'x', { duration: 0.7, ease: 'power3.out' }),
                    y: gsap.quickTo(el, 'y', { duration: 0.7, ease: 'power3.out' }),
                }));
                gsap.set(card, { transformPerspective: 900 });
                gsap.to(card, { y: -6, duration: 0.5, ease: 'power3.out' });
            };

            card.addEventListener('pointerenter', () => {
                rect = card.getBoundingClientRect();
                card.classList.add('is-tilting');
                if (!gsap.isTweening(card)) arm();
            });
            card.addEventListener('pointermove', (e) => {
                if (!rotX) {
                    if (gsap.isTweening(card)) return;
                    arm();
                }
                if (!rect) rect = card.getBoundingClientRect();
                const px = (e.clientX - rect.left) / rect.width;
                const py = (e.clientY - rect.top) / rect.height;
                card.style.setProperty('--mx', `${px * 100}%`);
                card.style.setProperty('--my', `${py * 100}%`);
                rotY((px - 0.5) * 10);
                rotX((0.5 - py) * 10);
                layers.forEach((l) => {
                    l.x((px - 0.5) * l.depth);
                    l.y((py - 0.5) * l.depth);
                });
            });
            card.addEventListener('pointerleave', () => {
                rect = null;
                card.classList.remove('is-tilting');
                rotX = rotY = null;
                gsap.to(card, { rotationX: 0, rotationY: 0, y: 0, duration: 0.8, ease: 'elastic.out(1, 0.6)', overwrite: true });
                depthEls.forEach(([el]) => gsap.to(el, { x: 0, y: 0, duration: 0.8, ease: 'elastic.out(1, 0.6)', overwrite: true }));
                layers = [];
            });
        });
    }

    /* ------------------------------------------------------------------
       Chart tooltips (hover and keyboard focus)
    ------------------------------------------------------------------ */
    function tooltips() {
        const targets = document.querySelectorAll('[data-tip]');
        if (!targets.length) return;
        const tip = document.createElement('div');
        tip.className = 'chart-tooltip';
        tip.setAttribute('role', 'tooltip');
        document.body.appendChild(tip);

        const show = (el, x, y) => {
            tip.textContent = el.dataset.tip;
            tip.style.left = `${x}px`;
            tip.style.top = `${y}px`;
            tip.classList.add('is-visible');
        };
        const hide = () => tip.classList.remove('is-visible');

        targets.forEach((el) => {
            el.addEventListener('pointermove', (e) => show(el, e.clientX, e.clientY));
            el.addEventListener('pointerleave', hide);
            el.addEventListener('focus', () => {
                const r = el.getBoundingClientRect();
                show(el, r.left + r.width / 2, r.top);
            });
            el.addEventListener('blur', hide);
        });
        window.addEventListener('scroll', hide, { passive: true });
    }

    /* ------------------------------------------------------------------
       Page loader: lifts once the page is ready, returns on navigation
    ------------------------------------------------------------------ */
    const loader = document.querySelector('[data-loader]');
    const loaderBar = loader.querySelector('[data-loader-bar]');
    const loadedCallbacks = [];
    let loaderDone = false;

    function whenLoaded(fn) {
        loaderDone ? fn() : loadedCallbacks.push(fn);
    }

    function hideLoader() {
        if (loaderDone) return;
        loaderDone = true;
        const finish = () => {
            loader.classList.add('is-done');
            loadedCallbacks.splice(0).forEach((fn) => fn());
        };
        // Timer rather than a tween callback: animation frames pause in background
        // tabs, and the loader must still lift there.
        if (motionOK) {
            gsap.to(loaderBar, { scaleX: 1, duration: 0.3, ease: 'power2.out', overwrite: true });
            setTimeout(finish, 320);
        } else {
            finish();
        }
    }

    const barProgress = () => (motionOK ? gsap.getProperty(loaderBar, 'scaleX') : 0);

    function initLoader() {
        const started = performance.now();
        // Arriving from another page: the inline script already placed the bar where
        // the previous page left it, so carry on from there without a minimum delay.
        const continuing = 'continue' in loader.dataset;

        if (motionOK) {
            const from = continuing ? parseFloat(loader.dataset.continue) || 0.5 : 0.08;
            gsap.fromTo(loaderBar, { scaleX: from }, { scaleX: 0.95, duration: 3, ease: 'power3.out' });
        }

        // Wait for the page (fonts, images, 3D libraries) — but never longer than 1.8s,
        // and on a first visit show it for at least 350ms so it doesn't flash.
        const ready = () => {
            const wait = continuing ? 0 : Math.max(0, 350 - (performance.now() - started));
            setTimeout(hideLoader, motionOK ? wait : 0);
        };
        document.readyState === 'complete' ? ready() : window.addEventListener('load', ready, { once: true });
        setTimeout(hideLoader, 1800);
    }

    function saveProgress() {
        try {
            sessionStorage.setItem('loader-progress', JSON.stringify({ p: barProgress(), t: Date.now() }));
        } catch (e) { /* storage unavailable: the next page simply starts fresh */ }
    }

    function showLoader(onShown) {
        loader.classList.remove('is-done');
        loader.classList.add('is-leaving');
        if (motionOK) {
            // A long, decelerating fill keeps the bar moving the whole time the
            // browser waits for the next page, instead of stalling halfway.
            gsap.fromTo(loaderBar, { scaleX: 0.04 }, { scaleX: 0.92, duration: 6, ease: 'expo.out', overwrite: true });
        }
        navigating = true;
        setTimeout(() => { saveProgress(); onShown(); }, motionOK ? 280 : 0);
    }
    let navigating = false;

    /* ------------------------------------------------------------------
       Page transitions between Laravel-rendered pages
    ------------------------------------------------------------------ */
    function pageTransitions() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            if (link.target === '_blank' || link.hasAttribute('download')) return;
            const url = new URL(link.href, location.href);
            if (url.origin !== location.origin) return;
            if (url.pathname === location.pathname && url.hash) return;
            if (url.href === location.href) return;

            e.preventDefault();
            showLoader(() => { location.href = url.href; });
        });

        // Record the bar's final position as the old page unloads.
        window.addEventListener('pagehide', () => { if (navigating) saveProgress(); });

        // Coming back via the back/forward cache: lift the loader again.
        window.addEventListener('pageshow', (e) => {
            if (!e.persisted) return;
            navigating = false;
            loader.classList.remove('is-leaving');
            loader.classList.add('is-done');
        });
    }

    /* ------------------------------------------------------------------
       Lifecycle
    ------------------------------------------------------------------ */
    window.addEventListener('pagehide', (e) => {
        // Only fully tear down when the page is really unloading.
        if (e.persisted) return;
        teardownFns.forEach((fn) => { try { fn(); } catch (err) { /* ignore */ } });
        if (motionOK) {
            ScrollTrigger.getAll().forEach((t) => t.kill());
            gsap.globalTimeline.clear();
        }
    });

    /* ------------------------------------------------------------------
       Nav indicator: sits on the current page, glides to hovered links
    ------------------------------------------------------------------ */
    function navIndicator() {
        const track = document.querySelector('[data-nav-track]');
        if (!track) return;
        const indicator = track.querySelector('[data-nav-indicator]');
        const list = track.querySelector('.site-nav__links');
        const links = Array.from(track.querySelectorAll('.site-nav__link'));
        const active = links.find((l) => l.classList.contains('is-active'));

        const moveTo = (link) => {
            if (!link) {
                indicator.classList.remove('is-ready');
                return;
            }
            indicator.style.width = `${link.offsetWidth}px`;
            indicator.style.transform = `translateX(${list.offsetLeft + link.offsetLeft}px)`;
            indicator.classList.add('is-ready');
            links.forEach((l) => l.classList.toggle('is-hovered', l === link && l !== active));
        };

        // First placement without animation.
        indicator.style.transition = 'none';
        moveTo(active);
        indicator.offsetWidth; // flush styles
        indicator.style.transition = '';
        track.classList.add('has-indicator');

        links.forEach((link) => {
            link.addEventListener('mouseenter', () => moveTo(link));
            link.addEventListener('focus', () => moveTo(link));
        });
        track.addEventListener('mouseleave', () => moveTo(active));
        track.addEventListener('focusout', (e) => { if (!track.contains(e.relatedTarget)) moveTo(active); });

        const reposition = () => {
            indicator.style.transition = 'none';
            moveTo(active);
            requestAnimationFrame(() => { indicator.style.transition = ''; });
        };
        window.addEventListener('resize', reposition);
        if (document.fonts) document.fonts.ready.then(reposition);
    }

    /* ------------------------------------------------------------------
       Custom cursor: dot + trailing ring (mouse devices only)
    ------------------------------------------------------------------ */
    function customCursor() {
        if (!finePointer.matches) return;

        const dot = document.createElement('div');
        const ring = document.createElement('div');
        dot.className = 'cursor-dot';
        ring.className = 'cursor-ring';
        ring.innerHTML = '<span class="cursor-ring__label"></span>';
        dot.setAttribute('aria-hidden', 'true');
        ring.setAttribute('aria-hidden', 'true');
        document.body.append(ring, dot);
        const label = ring.firstChild;

        const pos = { x: -100, y: -100 };
        const trail = { x: -100, y: -100 };
        const lag = reducedMotion.matches ? 1 : 0.18;
        let frame = 0;
        let visible = false;

        const interactive = 'a, button, [role="tab"], select, label, summary, [data-tilt], .filter-chip, .round-btn';
        const textual = 'input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="range"]), textarea, [contenteditable="true"]';

        const render = () => {
            trail.x += (pos.x - trail.x) * lag;
            trail.y += (pos.y - trail.y) * lag;
            dot.style.transform = `translate3d(${pos.x}px, ${pos.y}px, 0)`;
            ring.style.transform = `translate3d(${trail.x}px, ${trail.y}px, 0)`;
            // Keep animating only while the ring is still catching up.
            if (Math.abs(pos.x - trail.x) > 0.1 || Math.abs(pos.y - trail.y) > 0.1) {
                frame = requestAnimationFrame(render);
            } else {
                frame = 0;
            }
        };

        const show = (on) => {
            visible = on;
            root.classList.toggle('cursor-active', on);
        };

        document.addEventListener('mousemove', (e) => {
            pos.x = e.clientX;
            pos.y = e.clientY;
            if (!visible) {
                trail.x = pos.x;
                trail.y = pos.y;
                show(true);
            }
            if (!frame) frame = requestAnimationFrame(render);

            const target = e.target instanceof Element ? e.target : null;
            const overText = target && target.closest(textual);
            const overLink = target && !overText && target.closest(interactive);
            const dragZone = target && target.closest('[data-slider], [data-globe], [data-compare]');

            root.classList.toggle('cursor-text', !!overText);
            ring.classList.toggle('is-hover', !!overLink);
            ring.classList.toggle('is-drag', !overLink && !!dragZone);
            label.textContent = !overLink && dragZone ? 'Drag' : '';
        }, { passive: true });

        // Leaving the window (or the page losing focus) hands back the normal arrow.
        document.documentElement.addEventListener('mouseleave', () => show(false));
        window.addEventListener('blur', () => show(false));
        document.addEventListener('mousedown', () => ring.classList.add('is-pressed'));
        document.addEventListener('mouseup', () => ring.classList.remove('is-pressed'));
    }

    /* ------------------------------------------------------------------
       Smooth scrolling (Lenis), driven by GSAP's ticker so scroll position
       and scroll-triggered animations update in the same frame.
    ------------------------------------------------------------------ */
    function smoothScroll() {
        if (!motionOK || typeof window.Lenis === 'undefined') return null;

        // lerp: each frame closes 11% of the gap to the target, so the page follows
        // the wheel closely instead of gliding on for a fixed second after every notch.
        const instance = new Lenis({
            lerp: 0.11,
            smoothWheel: true,
            wheelMultiplier: 1,
            touchMultiplier: 1.2,
            // Let these scroll natively: menus, dialogs and anything opted out.
            prevent: (node) => !!(node.closest && node.closest('[data-lenis-prevent], dialog, .mobile-menu')),
        });
        instance.on('scroll', ScrollTrigger.update);
        gsap.ticker.add((time) => instance.raf(time * 1000));
        gsap.ticker.lagSmoothing(0);

        // In-page links glide to their target, clearing the fixed header.
        const offset = () => -(header.offsetHeight + 16);
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href*="#"]');
            if (!link || e.defaultPrevented) return;
            const url = new URL(link.href, location.href);
            if (url.pathname !== location.pathname || !url.hash || url.hash === '#') return;
            const target = document.querySelector(url.hash);
            if (!target) return;
            e.preventDefault();
            instance.scrollTo(target, { offset: offset() });
            history.pushState(null, '', url.hash);
            // Keep keyboard focus in step (e.g. the "Skip to content" link).
            if (target.matches('[tabindex], a, button, input, select, textarea')) target.focus({ preventScroll: true });
        });
        // Arriving with a hash: jump there once layout has settled.
        if (location.hash) {
            window.addEventListener('load', () => {
                const target = document.querySelector(location.hash);
                if (target) instance.scrollTo(target, { offset: offset(), immediate: true });
            }, { once: true });
        }

        teardownFns.push(() => instance.destroy());
        return instance;
    }

    tooltips();
    navIndicator();
    customCursor();
    initLoader();
    pageTransitions();
    lenis = smoothScroll();
    window.App.lenis = lenis;

    if (motionOK) {
        const intro = heroIntro();
        if (intro) whenLoaded(() => intro.play());
        scrollReveals();
        counters();
        if (finePointer.matches) tiltCards();
        // Recalculate trigger positions once fonts and images settle.
        window.addEventListener('load', () => ScrollTrigger.refresh());
    }
})();
