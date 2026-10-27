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
       Project slider: native scroll-snap + buttons + mouse drag
    ------------------------------------------------------------------ */
    const slider = document.querySelector('[data-slider]');
    if (slider) {
        const step = () => {
            const item = slider.querySelector('.project-slider__item');
            return item ? item.getBoundingClientRect().width + 20 : 400;
        };
        const behavior = window.App && window.App.reducedMotion ? 'auto' : 'smooth';
        document.querySelector('[data-slider-prev]')?.addEventListener('click', () => slider.scrollBy({ left: -step(), behavior }));
        document.querySelector('[data-slider-next]')?.addEventListener('click', () => slider.scrollBy({ left: step(), behavior }));

        slider.addEventListener('keydown', (e) => {
            if (e.target !== slider) return;
            if (e.key === 'ArrowRight') { e.preventDefault(); slider.scrollBy({ left: step(), behavior }); }
            if (e.key === 'ArrowLeft') { e.preventDefault(); slider.scrollBy({ left: -step(), behavior }); }
        });

        // Mouse drag (touch already scrolls natively).
        let startX = 0;
        let startScroll = 0;
        let dragging = false;
        let moved = false;

        slider.addEventListener('pointerdown', (e) => {
            if (e.pointerType !== 'mouse' || e.button !== 0) return;
            dragging = true;
            moved = false;
            startX = e.clientX;
            startScroll = slider.scrollLeft;
        });
        window.addEventListener('pointermove', (e) => {
            if (!dragging) return;
            const dx = e.clientX - startX;
            if (Math.abs(dx) > 5 && !moved) {
                moved = true;
                slider.classList.add('is-dragging');
            }
            if (moved) slider.scrollLeft = startScroll - dx;
        });
        window.addEventListener('pointerup', () => {
            if (!dragging) return;
            dragging = false;
            if (moved) {
                slider.classList.remove('is-dragging');
                // Let snap settle on the nearest card.
                const s = step();
                slider.scrollTo({ left: Math.round(slider.scrollLeft / s) * s, behavior });
            }
        });
        // Swallow the click that ends a drag so cards/links don't fire.
        slider.addEventListener('click', (e) => {
            if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; }
        }, true);

        if (motionOK) {
            gsap.from(slider.querySelectorAll('.project-slider__item'), {
                x: 80,
                opacity: 0,
                stagger: 0.12,
                duration: 1,
                // Remove the inline transform afterwards so no card can stay offset.
                clearProps: 'transform,opacity',
                scrollTrigger: { trigger: slider, start: 'top 85%', once: true },
            });
        }
    }
})();
