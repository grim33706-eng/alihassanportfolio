/**
 * Shared plumbing for the in-page (below the hero) 3D scenes.
 *
 * lazyScene(): Three.js itself is only downloaded once a scene's section comes
 * near the viewport, and the scene is built when the browser is idle.
 * runLoop(): renders only while the scene is on screen and the tab is visible,
 * drops to 1x resolution after sustained slow frames, draws a single still
 * frame for reduced motion, and cleans up on page teardown.
 */

// Phones, small screens, 4-core-or-less CPUs and data-saver mode get a lighter render.
export const lowPower = window.matchMedia('(max-width: 767px)').matches
    || (navigator.hardwareConcurrency || 8) <= 4
    || Boolean(navigator.connection && navigator.connection.saveData);

export const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const whenIdle = window.requestIdleCallback || ((fn) => setTimeout(fn, 200));

export function webglAvailable() {
    try {
        const c = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl')));
    } catch (e) {
        return false;
    }
}

/** Build `build(THREE, el)` for each element once it is within ~400px of the viewport. */
export function lazyScene(selector, build) {
    const els = [...document.querySelectorAll(selector)];
    if (!els.length || !webglAvailable()) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            io.unobserve(entry.target);
            whenIdle(async () => {
                try {
                    const THREE = await import('three');
                    build(THREE, entry.target);
                } catch (error) {
                    // The section reads fine without its scene.
                    console.warn('3D scene unavailable:', error);
                }
            }, { timeout: 600 });
        });
    }, { rootMargin: '400px 0px' });
    els.forEach((el) => io.observe(el));
}

export function makeRenderer(THREE, container) {
    const renderer = new THREE.WebGLRenderer({ antialias: !lowPower, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, lowPower ? 1 : 1.5));
    renderer.setClearColor(0x000000, 0);
    container.prepend(renderer.domElement);
    return renderer;
}

/**
 * Drive a scene. `frame(t, dt)` draws; `resize()` re-fits to the container.
 * Returns { dispose } for anything extra the caller wants cleaned up.
 */
export function runLoop(THREE, { container, renderer, scene, camera, frame, resize, disposables = [] }) {
    const clock = new THREE.Clock(false);
    let t = 0;
    let running = false;
    let inView = false;
    let raf = 0;
    let slow = 0;
    let downgraded = false;

    const loop = () => {
        if (!running) return;
        const dt = Math.min(clock.getDelta(), 1 / 30);
        t += dt;
        if (!downgraded && dt > 1 / 40) {
            if (++slow > 45) {
                downgraded = true;
                renderer.setPixelRatio(1);
                resize();
            }
        } else if (slow > 0) {
            slow--;
        }
        frame(t, dt);
        renderer.render(scene, camera);
        raf = requestAnimationFrame(loop);
    };
    const start = () => {
        if (running || reducedMotion) return;
        running = true;
        clock.start();
        raf = requestAnimationFrame(loop);
    };
    const stop = () => {
        running = false;
        cancelAnimationFrame(raf);
    };
    const still = () => {
        frame(4, 0);
        renderer.render(scene, camera);
    };

    resize();
    renderer.compile(scene, camera);

    const io = new IntersectionObserver(([entry]) => {
        inView = entry.isIntersecting;
        inView && !document.hidden ? start() : stop();
    });
    io.observe(container);
    const onVisibility = () => (document.hidden || !inView ? stop() : start());
    document.addEventListener('visibilitychange', onVisibility);

    let timer;
    const onResize = () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            resize();
            if (reducedMotion) still();
        }, 120);
    };
    window.addEventListener('resize', onResize);

    container.classList.add('webgl-ready');
    if (reducedMotion) still();

    const dispose = () => {
        stop();
        io.disconnect();
        document.removeEventListener('visibilitychange', onVisibility);
        window.removeEventListener('resize', onResize);
        disposables.forEach((d) => d && d.dispose && d.dispose());
        renderer.dispose();
        renderer.forceContextLoss();
    };
    window.App?.onTeardown(dispose);
    return { dispose };
}

export function glowTexture(THREE) {
    const size = 64;
    const c = document.createElement('canvas');
    c.width = c.height = size;
    const ctx = c.getContext('2d');
    const g = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
    g.addColorStop(0, 'rgba(255,255,255,1)');
    g.addColorStop(0.25, 'rgba(255,255,255,0.55)');
    g.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, size, size);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

/** Vertical gradient: opaque at the top (v = 1), clear at the bottom. */
export function fadeTexture(THREE) {
    const c = document.createElement('canvas');
    c.width = 4;
    c.height = 128;
    const ctx = c.getContext('2d');
    const g = ctx.createLinearGradient(0, 0, 0, 128);
    g.addColorStop(0, 'rgba(255,255,255,0.95)');
    g.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, 4, 128);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}
