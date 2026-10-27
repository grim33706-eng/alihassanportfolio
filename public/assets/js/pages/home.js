/**
 * Home page: Meta / Google Ads tabs, typing demo and the project slider.
 */
(function () {
    'use strict';

    const motionOK = window.App && window.App.motionOK;

    /* ------------------------------------------------------------------
       Platform tabs (WAI-ARIA tabs pattern)
    ------------------------------------------------------------------ */
    const tabsRoot = document.querySelector('[data-tabs]');
    if (tabsRoot) {
        const tabs = Array.from(tabsRoot.querySelectorAll('[role="tab"]'));
        const panels = Array.from(tabsRoot.querySelectorAll('[role="tabpanel"]'));
        const indicator = tabsRoot.querySelector('[data-tab-indicator]');
        let current = tabs.findIndex((t) => t.getAttribute('aria-selected') === 'true');
        let animating = null;

        const placeIndicator = (tab, instant) => {
            const props = { x: tab.offsetLeft - 6, width: tab.offsetWidth };
            if (motionOK && !instant) {
                gsap.to(indicator, { ...props, duration: 0.55, ease: 'power3.inOut' });
            } else if (motionOK) {
                gsap.set(indicator, props);
            } else {
                indicator.style.transform = `translateX(${props.x}px)`;
                indicator.style.width = `${props.width}px`;
            }
        };

        const activate = (index, focus) => {
            if (index === current) return;
            const prevPanel = panels[current];
            const nextPanel = panels[index];
            const direction = index > current ? 1 : -1;

            tabs.forEach((tab, i) => {
                const selected = i === index;
                tab.setAttribute('aria-selected', String(selected));
                tab.tabIndex = selected ? 0 : -1;
            });
            if (focus) tabs[index].focus();
            tabsRoot.dataset.active = tabs[index].dataset.tab;
            placeIndicator(tabs[index]);
            current = index;

            if (!motionOK) {
                prevPanel.hidden = true;
                nextPanel.hidden = false;
                return;
            }

            if (animating) animating.progress(1);
            animating = gsap.timeline({ onComplete: () => { animating = null; } })
                .to(prevPanel, { opacity: 0, x: -30 * direction, duration: 0.25, ease: 'power2.in' })
                .add(() => {
                    prevPanel.hidden = true;
                    gsap.set(prevPanel, { clearProps: 'opacity,transform' });
                    nextPanel.hidden = false;
                })
                .fromTo(nextPanel, { opacity: 0, x: 30 * direction }, { opacity: 1, x: 0, duration: 0.5, clearProps: 'transform' })
                .from(nextPanel.querySelectorAll('[data-panel-item]'), { y: 24, opacity: 0, stagger: 0.05, duration: 0.55, clearProps: 'all' }, '-=0.35');
        };

        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => activate(i, false));
            tab.addEventListener('keydown', (e) => {
                let next = null;
                if (e.key === 'ArrowRight') next = (i + 1) % tabs.length;
                if (e.key === 'ArrowLeft') next = (i - 1 + tabs.length) % tabs.length;
                if (e.key === 'Home') next = 0;
                if (e.key === 'End') next = tabs.length - 1;
                if (next !== null) {
                    e.preventDefault();
                    activate(next, true);
                }
            });
        });

        tabsRoot.dataset.active = tabs[current].dataset.tab;
        tabsRoot.dataset.ready = '';
        placeIndicator(tabs[current], true);
        window.addEventListener('resize', () => placeIndicator(tabs[current], true));
        document.fonts && document.fonts.ready.then(() => placeIndicator(tabs[current], true));
    }

    /* ------------------------------------------------------------------
       Typing demo in the (illustrative) Google search bar
    ------------------------------------------------------------------ */
    const typing = document.querySelector('[data-typing]');
    if (typing && motionOK) {
        const phrases = typing.dataset.typing.split('|');
        let p = 0;
        let timer = null;

        const type = (text, i = 0) => {
            typing.textContent = text.slice(0, i);
            if (i < text.length) {
                timer = setTimeout(() => type(text, i + 1), 55);
            } else {
                timer = setTimeout(erase, 2200);
            }
        };
        const erase = () => {
            const t = typing.textContent;
            if (t.length) {
                typing.textContent = t.slice(0, -1);
                timer = setTimeout(erase, 25);
            } else {
                p = (p + 1) % phrases.length;
                timer = setTimeout(() => type(phrases[p]), 300);
            }
        };

        // Only run while the Google panel is visible.
        const panel = typing.closest('[role="tabpanel"]');
        new MutationObserver(() => {
            clearTimeout(timer);
            if (!panel.hidden) type(phrases[p]);
        }).observe(panel, { attributes: true, attributeFilter: ['hidden'] });

        window.App.onTeardown(() => clearTimeout(timer));
    }

    /* ------------------------------------------------------------------
       Selected work showcase: centred feature slide, neighbours in depth,
       drag/swipe, keyboard, pagination with autoplay progress.
       Without GSAP or with reduced motion it stays a native scroll-snap row.
    ------------------------------------------------------------------ */
    const showcase = document.querySelector('[data-showcase]');
    if (showcase) {
        const stage = showcase.querySelector('[data-showcase-stage]');
        const track = showcase.querySelector('[data-showcase-track]');
        const slides = Array.from(showcase.querySelectorAll('[data-showcase-slide]'));
        const dots = Array.from(showcase.querySelectorAll('[data-showcase-dot]'));
        const current = showcase.querySelector('[data-showcase-current]');
        const live = showcase.querySelector('[data-showcase-live]');
        const toggle = showcase.querySelector('[data-showcase-toggle]');
        const count = slides.length;
        const AUTOPLAY = 7; // seconds per slide
        let index = 0;

        const pad = (n) => String(n).padStart(2, '0');
        const announce = (i) => { live.textContent = `Slide ${i + 1} of ${count}: ${slides[i].getAttribute('aria-label').replace(/^\d+ of \d+: /, '')}`; };
        const setPager = (i) => {
            current.textContent = pad(i + 1);
            dots.forEach((d, k) => (k === i ? d.setAttribute('aria-current', 'true') : d.removeAttribute('aria-current')));
        };

        if (!motionOK) {
            // Native mode: buttons and dots scroll the row; the pager follows the scroll.
            const goNative = (i) => {
                index = (i + count) % count;
                slides[index].scrollIntoView({ behavior: 'auto', inline: 'center', block: 'nearest' });
                setPager(index);
            };
            showcase.querySelector('[data-showcase-prev]').addEventListener('click', () => goNative(index - 1));
            showcase.querySelector('[data-showcase-next]').addEventListener('click', () => goNative(index + 1));
            dots.forEach((d, k) => d.addEventListener('click', () => goNative(k)));
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting && e.intersectionRatio > 0.6) {
                        index = slides.indexOf(e.target);
                        setPager(index);
                    }
                });
            }, { root: stage, threshold: [0.6] });
            slides.forEach((s) => io.observe(s));
        } else {
            showcase.classList.add('is-enhanced');
            const fills = dots.map((d) => d.querySelector('[data-showcase-fill]'));
            gsap.set(slides, { transformPerspective: 1600 });

            let slideW = 0;
            let gap = 0;
            const measure = () => {
                slideW = slides[0].offsetWidth;
                gap = parseFloat(getComputedStyle(track).columnGap) || 0;
            };
            const offsetFor = (i) => (stage.clientWidth - slideW) / 2 - i * (slideW + gap);

            // Depth for each slide relative to the active one.
            const depth = (d) => {
                const a = Math.abs(d);
                return {
                    scale: a === 0 ? 1 : 0.86,
                    opacity: a === 0 ? 1 : a === 1 ? 0.4 : 0.12,
                    rotateY: d === 0 ? 0 : d < 0 ? 9 : -9,
                };
            };

            const revealActive = (slide) => {
                const canvas = slide.querySelector('[data-showcase-canvas]');
                const line = slide.querySelector('.showcase-slide__line');
                const dot = slide.querySelector('.showcase-slide__dot');
                const info = slide.querySelectorAll('.showcase-slide__info > *');
                const tl = gsap.timeline();
                tl.fromTo(canvas, { scale: 1.12 }, { scale: 1, duration: 1.2, ease: 'power3.out' }, 0);
                if (line) tl.fromTo(line, { strokeDashoffset: 1 }, { strokeDashoffset: 0, duration: 1.3, ease: 'power2.inOut' }, 0.1);
                if (dot) tl.fromTo(dot, { scale: 0, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.5, ease: 'back.out(2.5)' }, 1.15);
                tl.fromTo(info, { y: 22, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, stagger: 0.06, ease: 'power3.out', clearProps: 'transform' }, 0.15);
            };

            // Autoplay: progress fills the active dot, then advances.
            let playing = !window.App.reducedMotion;
            let hovered = false;
            let focused = false;
            let dragging = false;
            let inView = false;
            let autoTween = null;

            const shouldRun = () => playing && !hovered && !focused && !dragging && inView && !document.hidden;
            const syncAutoplay = () => {
                if (!autoTween) return;
                shouldRun() ? autoTween.resume() : autoTween.pause();
            };
            const startProgress = () => {
                if (autoTween) autoTween.kill();
                fills.forEach((f, k) => gsap.set(f, { scaleX: k === index && !playing ? 1 : 0 }));
                if (!playing) { autoTween = null; return; }
                autoTween = gsap.fromTo(fills[index], { scaleX: 0 }, {
                    scaleX: 1,
                    duration: AUTOPLAY,
                    ease: 'none',
                    paused: true,
                    onComplete: () => go(index + 1, { auto: true }),
                });
                syncAutoplay();
            };

            function go(i, { instant = false, auto = false } = {}) {
                const prev = index;
                index = (i + count) % count;
                const changed = prev !== index;

                gsap.to(track, { x: offsetFor(index), duration: instant ? 0 : 0.95, ease: 'power3.inOut', overwrite: true });
                slides.forEach((s, k) => {
                    const d = k - index;
                    gsap.to(s, { ...depth(d), duration: instant ? 0 : 0.95, ease: 'power3.inOut', overwrite: 'auto' });
                    const active = d === 0;
                    s.classList.toggle('is-active', active);
                    s.setAttribute('aria-hidden', String(!active));
                    // Keep links in background slides out of the tab order.
                    s.querySelectorAll('a, button').forEach((el) => { el.tabIndex = active ? 0 : -1; });
                });
                setPager(index);
                if (changed && !instant) revealActive(slides[index]);
                if (changed && !auto) announce(index);
                startProgress();
            }

            // Controls
            showcase.querySelector('[data-showcase-prev]').addEventListener('click', () => go(index - 1));
            showcase.querySelector('[data-showcase-next]').addEventListener('click', () => go(index + 1));
            dots.forEach((d, k) => d.addEventListener('click', () => go(k)));
            stage.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight') { e.preventDefault(); go(index + 1); }
                if (e.key === 'ArrowLeft') { e.preventDefault(); go(index - 1); }
                if (e.key === 'Home') { e.preventDefault(); go(0); }
                if (e.key === 'End') { e.preventDefault(); go(count - 1); }
            });

            if (playing) {
                toggle.hidden = false;
                toggle.addEventListener('click', () => {
                    playing = !playing;
                    showcase.classList.toggle('is-paused', !playing);
                    toggle.setAttribute('aria-label', playing ? 'Pause automatic slideshow' : 'Play automatic slideshow');
                    startProgress();
                });
            }

            // Clicking a neighbour brings it forward instead of following its links.
            slides.forEach((s, k) => {
                s.addEventListener('click', (e) => {
                    if (k === index) return;
                    e.preventDefault();
                    go(k);
                });
            });

            // Drag / swipe (mouse and touch; vertical page scroll stays native).
            let startX = 0;
            let deltaX = 0;
            let pointerId = null;
            let moved = false;
            stage.addEventListener('pointerdown', (e) => {
                if (e.button !== 0) return;
                pointerId = e.pointerId;
                startX = e.clientX;
                deltaX = 0;
                moved = false;
            });
            stage.addEventListener('pointermove', (e) => {
                if (pointerId !== e.pointerId) return;
                deltaX = e.clientX - startX;
                if (!moved && Math.abs(deltaX) > 8) {
                    moved = true;
                    dragging = true;
                    stage.classList.add('is-dragging');
                    stage.setPointerCapture(pointerId);
                    syncAutoplay();
                }
                if (moved) gsap.set(track, { x: offsetFor(index) + deltaX * 0.9 });
            });
            const endDrag = (e) => {
                if (pointerId !== e.pointerId) return;
                pointerId = null;
                if (!moved) return;
                dragging = false;
                stage.classList.remove('is-dragging');
                const threshold = Math.min(120, slideW * 0.15);
                if (deltaX < -threshold) go(index + 1);
                else if (deltaX > threshold) go(index - 1);
                else go(index);
            };
            stage.addEventListener('pointerup', endDrag);
            stage.addEventListener('pointercancel', endDrag);
            // A drag must not also count as a click on a slide or link.
            stage.addEventListener('click', (e) => {
                if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; }
            }, true);

            // Pause conditions
            showcase.addEventListener('pointerenter', () => { hovered = true; syncAutoplay(); });
            showcase.addEventListener('pointerleave', () => { hovered = false; syncAutoplay(); });
            showcase.addEventListener('focusin', () => { focused = true; syncAutoplay(); });
            showcase.addEventListener('focusout', (e) => { if (!showcase.contains(e.relatedTarget)) { focused = false; syncAutoplay(); } });
            document.addEventListener('visibilitychange', syncAutoplay);
            new IntersectionObserver(([entry]) => { inView = entry.isIntersecting; syncAutoplay(); }, { threshold: 0.35 }).observe(stage);

            // Layout
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => { measure(); go(index, { instant: true }); }, 120);
            });
            measure();
            go(0, { instant: true });
            if (document.fonts) document.fonts.ready.then(() => { measure(); go(index, { instant: true }); });

            // Entrance: the deck rises in and the first slide plays its reveal.
            gsap.from(stage, {
                y: 60,
                autoAlpha: 0,
                duration: 1.1,
                ease: 'power3.out',
                clearProps: 'transform,opacity,visibility',
                scrollTrigger: { trigger: stage, start: 'top 85%', once: true, onEnter: () => revealActive(slides[0]) },
            });
            gsap.from(showcase.querySelectorAll('.showcase__pager, .showcase__buttons'), {
                y: 20,
                duration: 0.8,
                delay: 0.2,
                clearProps: 'transform',
                scrollTrigger: { trigger: stage, start: 'top 85%', once: true },
            });

            window.App.onTeardown(() => { if (autoTween) autoTween.kill(); });
        }
    }
})();
