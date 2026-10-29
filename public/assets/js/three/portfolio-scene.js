/**
 * Portfolio hero 3D scene: a slowly turning carousel of glass campaign cards
 * around a glowing hub. Each card shows a real project's industry, platforms
 * and category (from data-cards) with an illustrative chart — no figures.
 * Light pulses run from the hub out to every card.
 *
 * Renders only while the hero is on screen and the tab is visible, at a
 * lighter quality on low-power devices. With reduced motion it draws one
 * still frame. Without WebGL the CSS card fallback in the markup stays.
 */
import * as THREE from 'three';

const container = document.querySelector('[data-portfolio-scene]');

function webglAvailable() {
    try {
        const c = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl')));
    } catch (e) {
        return false;
    }
}

// Phones, small screens, 4-core-or-less CPUs and data-saver mode get a lighter render.
const lowPower = window.matchMedia('(max-width: 767px)').matches
    || (navigator.hardwareConcurrency || 8) <= 4
    || Boolean(navigator.connection && navigator.connection.saveData);

// Build the scene once the browser is idle after first paint, so it doesn't
// compete with the page loader and the opening animations.
const whenIdle = window.requestIdleCallback || ((fn) => setTimeout(fn, 200));

// The scene sits beside the headline; below this width it is hidden by CSS, so skip it entirely.
const wideEnough = window.matchMedia('(min-width: 992px)').matches;

if (container && wideEnough && webglAvailable()) {
    whenIdle(() => {
        // Card text is drawn to canvases, so wait for the web fonts first.
        (document.fonts?.ready || Promise.resolve()).then(init);
    }, { timeout: 800 });
}

const PLATFORM_COLORS = { meta: '#5b8cff', google: '#3ddc97', tiktok: '#ff5c8a' };
const PLATFORM_SHORT = { meta: 'Meta', google: 'Google', tiktok: 'TikTok' };
const CARD_W = 2.35;
const CARD_H = 1.55;

function init() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let data = [];
    try { data = JSON.parse(container.dataset.cards || '[]'); } catch (e) { data = []; }
    if (!data.length) return;
    const cardsData = data.slice(0, lowPower ? 6 : 8);

    const renderer = new THREE.WebGLRenderer({ antialias: !lowPower, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, lowPower ? 1 : 1.5));
    renderer.setClearColor(0x000000, 0);
    container.prepend(renderer.domElement);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 60);
    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };
    const glowTex = track(makeGlowTexture());
    const maxAniso = renderer.capabilities.getMaxAnisotropy();

    const world = new THREE.Group();
    scene.add(world);
    const ring = new THREE.Group(); // the carousel, tilted toward the camera
    ring.rotation.set(0.2, 0, -0.06);
    world.add(ring);

    // ---- Floor grid and orbit track
    const grid = new THREE.PolarGridHelper(4.6, 24, 5, 96, 0x4f7cff, 0x4f7cff);
    grid.material.transparent = true;
    grid.material.opacity = 0.08;
    grid.material.depthWrite = false;
    grid.position.y = -1.55;
    track(grid.geometry); track(grid.material);
    ring.add(grid);

    const RADIUS = 3.45;
    const orbit = new THREE.Mesh(
        track(new THREE.TorusGeometry(RADIUS, 0.008, 6, 200)),
        track(new THREE.MeshBasicMaterial({ color: 0x6d93ff, transparent: true, opacity: 0.35, blending: THREE.AdditiveBlending, depthWrite: false })),
    );
    orbit.rotation.x = Math.PI / 2;
    orbit.position.y = -0.95;
    ring.add(orbit);

    // ---- Hub: core, beam and spinning rings
    const hub = new THREE.Group();
    hub.position.y = -0.95;
    ring.add(hub);
    const hubCore = new THREE.Mesh(track(new THREE.IcosahedronGeometry(0.2, 2)), track(new THREE.MeshBasicMaterial({ color: 0xdfe6ff })));
    hub.add(hubCore);
    const hubGlow = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color: 0x6d93ff, transparent: true, opacity: 0.9, blending: THREE.AdditiveBlending, depthWrite: false })));
    hubGlow.scale.setScalar(1.8);
    hub.add(hubGlow);
    const hubShell = new THREE.Mesh(track(new THREE.IcosahedronGeometry(0.42, 1)), track(new THREE.MeshBasicMaterial({ color: 0x8b5cf6, wireframe: true, transparent: true, opacity: 0.4 })));
    hub.add(hubShell);
    const hubRings = [0.7, 0.95].map((r, i) => {
        const m = new THREE.Mesh(
            track(new THREE.TorusGeometry(r, 0.01, 6, 96)),
            track(new THREE.MeshBasicMaterial({ color: i ? 0x8b5cf6 : 0x4f7cff, transparent: true, opacity: 0.6, blending: THREE.AdditiveBlending, depthWrite: false })),
        );
        hub.add(m);
        return m;
    });
    const beam = new THREE.Mesh(
        track(new THREE.CylinderGeometry(0.06, 0.32, 3.2, 24, 1, true)),
        track(new THREE.MeshBasicMaterial({ map: track(makeBeamTexture()), color: 0x6d93ff, transparent: true, opacity: 0.5, blending: THREE.AdditiveBlending, depthWrite: false, side: THREE.DoubleSide })),
    );
    beam.position.y = 1.55;
    hub.add(beam);

    // ---- Cards
    const backTex = track(makeCardBack());
    const cardGeo = track(new THREE.PlaneGeometry(CARD_W, CARD_H));
    const cards = cardsData.map((d, i) => {
        const tex = track(makeCardTexture(d, i));
        tex.anisotropy = maxAniso;
        const group = new THREE.Group();
        const front = new THREE.Mesh(cardGeo, track(new THREE.MeshBasicMaterial({ map: tex, transparent: true, depthWrite: false })));
        const back = new THREE.Mesh(cardGeo, track(new THREE.MeshBasicMaterial({ map: backTex, transparent: true, depthWrite: false })));
        back.rotation.y = Math.PI;
        group.add(front, back);
        ring.add(group);
        const glow = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color: new THREE.Color(PLATFORM_COLORS[d.platforms[0]] || '#6d93ff'), transparent: true, opacity: 0.0, blending: THREE.AdditiveBlending, depthWrite: false })));
        glow.scale.set(CARD_W * 1.9, CARD_H * 1.9, 1);
        glow.position.z = -0.05;
        group.add(glow);
        return { group, front, back, glow, angle: (i / cardsData.length) * Math.PI * 2, phase: i * 1.7 };
    });

    // ---- Links from the hub to each card, with a pulse running outward
    const LINK_SEGS = 20;
    const linkMat = track(new THREE.LineBasicMaterial({ color: 0x6d93ff, transparent: true, opacity: 0.22, blending: THREE.AdditiveBlending, depthWrite: false }));
    const pulseMat = track(new THREE.SpriteMaterial({ map: glowTex, color: 0xbfd0ff, transparent: true, opacity: 0.95, blending: THREE.AdditiveBlending, depthWrite: false }));
    const links = cards.map(() => {
        const geo = track(new THREE.BufferGeometry());
        geo.setAttribute('position', new THREE.BufferAttribute(new Float32Array((LINK_SEGS + 1) * 3), 3));
        const line = new THREE.Line(geo, linkMat);
        ring.add(line);
        const pulse = new THREE.Sprite(pulseMat);
        pulse.scale.setScalar(0.32);
        ring.add(pulse);
        return { line, pulse };
    });

    // ---- Dust
    const DUST = lowPower ? 160 : 360;
    const dustPos = new Float32Array(DUST * 3);
    for (let i = 0; i < DUST; i++) {
        const r = 1 + Math.random() * 4.6;
        const a = Math.random() * Math.PI * 2;
        dustPos[i * 3] = Math.cos(a) * r;
        dustPos[i * 3 + 1] = -1.5 + Math.random() * 3.6;
        dustPos[i * 3 + 2] = Math.sin(a) * r;
    }
    const dustGeo = track(new THREE.BufferGeometry());
    dustGeo.setAttribute('position', new THREE.BufferAttribute(dustPos, 3));
    const dust = new THREE.Points(dustGeo, track(new THREE.PointsMaterial({ size: 0.06, map: glowTex, color: 0xa8b8ff, transparent: true, opacity: 0.55, depthWrite: false, blending: THREE.AdditiveBlending })));
    ring.add(dust);

    // ---- Layout
    function layout() {
        const width = container.clientWidth || 1;
        const height = container.clientHeight || 1;
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        const halfV = Math.tan(THREE.MathUtils.degToRad(camera.fov / 2));
        const needH = 2.55 / halfV;
        const needW = 4.9 / (halfV * camera.aspect);
        const dist = Math.max(needH, needW) + RADIUS * 0.35;
        camera.position.set(0, dist * 0.12, dist);
        camera.lookAt(0, 0.05, 0);
        camera.updateProjectionMatrix();
    }
    layout();

    // ---- Pointer parallax
    const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
    const onMove = (e) => {
        pointer.tx = e.clientX / window.innerWidth - 0.5;
        pointer.ty = e.clientY / window.innerHeight - 0.5;
    };
    if (!reduced) window.addEventListener('pointermove', onMove, { passive: true });

    // ---- Frame
    const heroH = container.closest('.page-hero')?.offsetHeight || 700;
    const clock = new THREE.Clock(false);
    const camPos = new THREE.Vector3();
    const cardWorld = new THREE.Vector3();
    const from = new THREE.Vector3();
    const to = new THREE.Vector3();
    const mid = new THREE.Vector3();
    let t = 0;
    let intro = 0;
    let running = false;
    let inView = true;
    let frame = 0;
    let slowFrames = 0;
    let downgraded = false;

    function renderFrame() {
        pointer.x += (pointer.tx - pointer.x) * 0.05;
        pointer.y += (pointer.ty - pointer.y) * 0.05;
        const scroll = Math.min(window.scrollY / heroH, 1);
        const spin = t * 0.12 + scroll * 1.1;

        world.rotation.y = pointer.x * 0.3;
        world.rotation.x = pointer.y * 0.07;
        world.position.y = -scroll * 0.8;
        world.updateMatrixWorld();
        camera.getWorldPosition(camPos);

        cards.forEach((c, i) => {
            const k = easeOutBack(THREE.MathUtils.clamp(intro * 1.9 - i * 0.09, 0, 1));
            const a = c.angle + spin;
            const bob = Math.sin(t * 1.1 + c.phase) * 0.09;
            c.group.position.set(Math.sin(a) * RADIUS, 0.15 + bob + (1 - k) * 1.2, Math.cos(a) * RADIUS);
            c.group.rotation.set(0, a, Math.sin(t * 0.8 + c.phase) * 0.025);
            c.group.scale.setScalar(Math.max(0.001, k));

            // Cards nearer the camera are brighter; the far side recedes.
            c.group.getWorldPosition(cardWorld);
            const facing = THREE.MathUtils.clamp((Math.cos(a) + 1) / 2, 0, 1);
            const vis = Math.min(1, k);
            c.front.material.opacity = (0.28 + facing * 0.72) * vis;
            c.back.material.opacity = (0.5 + (1 - facing) * 0.3) * vis;
            c.glow.material.opacity = Math.pow(facing, 3) * 0.32 * vis;

            // Link + pulse from the hub to the card's lower edge.
            from.set(0, -0.95, 0);
            to.set(c.group.position.x, c.group.position.y - CARD_H * 0.5, c.group.position.z);
            mid.copy(from).add(to).multiplyScalar(0.5);
            mid.y -= 0.35;
            const attr = links[i].line.geometry.attributes.position;
            for (let s = 0; s <= LINK_SEGS; s++) {
                const q = s / LINK_SEGS;
                const u = 1 - q;
                attr.setXYZ(s,
                    u * u * from.x + 2 * u * q * mid.x + q * q * to.x,
                    u * u * from.y + 2 * u * q * mid.y + q * q * to.y,
                    u * u * from.z + 2 * u * q * mid.z + q * q * to.z);
            }
            attr.needsUpdate = true;
            const q = (t * 0.45 + i * 0.37) % 1;
            const u = 1 - q;
            links[i].pulse.position.set(
                u * u * from.x + 2 * u * q * mid.x + q * q * to.x,
                u * u * from.y + 2 * u * q * mid.y + q * q * to.y,
                u * u * from.z + 2 * u * q * mid.z + q * q * to.z);
            links[i].pulse.material.opacity = 0.95 * vis;
            links[i].pulse.scale.setScalar(0.32 * Math.sin(q * Math.PI) * vis + 0.001);
        });
        linkMat.opacity = 0.22 * Math.min(1, intro * 1.5);

        const beat = 1 + Math.sin(t * 2.4) * 0.07;
        const e = easeOutCubic(Math.min(1, intro * 1.4));
        hubCore.scale.setScalar(beat * e + 0.001);
        hubGlow.scale.setScalar(1.8 * beat * e + 0.001);
        hubShell.rotation.set(t * 0.35, t * 0.5, 0);
        hubShell.scale.setScalar(e + 0.001);
        hubRings[0].rotation.set(Math.PI / 2 + Math.sin(t * 0.5) * 0.3, t * 0.4, 0);
        hubRings[1].rotation.set(Math.PI / 2 - Math.sin(t * 0.4) * 0.35, -t * 0.3, 0);
        hubRings.forEach((r) => r.scale.setScalar(e + 0.001));
        beam.material.opacity = 0.28 * e * (0.85 + Math.sin(t * 1.7) * 0.15);
        orbit.rotation.z = t * 0.05;
        dust.rotation.y = -t * 0.02;

        renderer.render(scene, camera);
    }

    function loop() {
        if (!running) return;
        const dt = Math.min(clock.getDelta(), 1 / 30);
        t += dt;
        intro = Math.min(1, intro + dt / 1.9);
        if (!downgraded && dt > 1 / 40) {
            if (++slowFrames > 45) {
                downgraded = true;
                renderer.setPixelRatio(1);
                layout();
            }
        } else if (slowFrames > 0) {
            slowFrames--;
        }
        renderFrame();
        frame = requestAnimationFrame(loop);
    }

    function renderStill() {
        t = 3;
        intro = 1;
        renderFrame();
    }

    function start() {
        if (running || reduced) return;
        running = true;
        clock.start();
        frame = requestAnimationFrame(loop);
    }
    function stop() {
        running = false;
        cancelAnimationFrame(frame);
    }

    renderer.compile(scene, camera);

    const io = new IntersectionObserver(([entry]) => {
        inView = entry.isIntersecting;
        inView && !document.hidden ? start() : stop();
    }, { threshold: 0 });
    io.observe(container);

    const onVisibility = () => (document.hidden || !inView ? stop() : start());
    document.addEventListener('visibilitychange', onVisibility);

    let resizeTimer;
    const onResize = () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            layout();
            if (reduced) renderStill();
        }, 120);
    };
    window.addEventListener('resize', onResize);

    container.classList.add('webgl-ready');
    reduced ? renderStill() : start();

    window.App?.onTeardown(() => {
        stop();
        io.disconnect();
        window.removeEventListener('pointermove', onMove);
        window.removeEventListener('resize', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        disposables.forEach((d) => d.dispose && d.dispose());
        renderer.dispose();
        renderer.forceContextLoss();
    });
}

/* ---------------------------------------------------------------------- */
/* Canvas textures                                                         */
/* ---------------------------------------------------------------------- */

const TEX_W = 640;
const TEX_H = Math.round(TEX_W * (CARD_H / CARD_W));

function roundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
}

function cardBase(ctx, accent) {
    ctx.clearRect(0, 0, TEX_W, TEX_H);
    roundRect(ctx, 3, 3, TEX_W - 6, TEX_H - 6, 30);
    const bg = ctx.createLinearGradient(0, 0, TEX_W, TEX_H);
    bg.addColorStop(0, 'rgba(27, 35, 56, 0.96)');
    bg.addColorStop(1, 'rgba(13, 19, 36, 0.96)');
    ctx.fillStyle = bg;
    ctx.fill();
    const edge = ctx.createLinearGradient(0, 0, TEX_W, TEX_H);
    edge.addColorStop(0, accent);
    edge.addColorStop(0.55, 'rgba(139, 92, 246, 0.55)');
    edge.addColorStop(1, 'rgba(41, 50, 71, 0.9)');
    ctx.strokeStyle = edge;
    ctx.lineWidth = 3;
    ctx.stroke();
}

function makeCardTexture(d, index) {
    const c = document.createElement('canvas');
    c.width = TEX_W;
    c.height = TEX_H;
    const ctx = c.getContext('2d');
    const accent = PLATFORM_COLORS[d.platforms[0]] || '#6d93ff';
    cardBase(ctx, accent);

    // Platform chips
    let x = 34;
    ctx.font = '600 22px "JetBrains Mono", ui-monospace, monospace';
    d.platforms.forEach((p) => {
        const label = (PLATFORM_SHORT[p] || p).toUpperCase();
        const w = ctx.measureText(label).width + 44;
        roundRect(ctx, x, 30, w, 38, 19);
        ctx.fillStyle = 'rgba(255,255,255,0.06)';
        ctx.fill();
        ctx.fillStyle = PLATFORM_COLORS[p] || '#6d93ff';
        ctx.beginPath();
        ctx.arc(x + 19, 49, 6, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = '#cfd6e6';
        ctx.fillText(label, x + 32, 57);
        x += w + 10;
    });

    // Industry and category
    ctx.fillStyle = '#f8fafc';
    ctx.font = '700 44px "Sora", "Inter", sans-serif';
    ctx.fillText(fit(ctx, d.industry, TEX_W - 68), 34, 130);
    ctx.fillStyle = '#8f9ab0';
    ctx.font = '500 27px "Inter", sans-serif';
    ctx.fillText(fit(ctx, d.category, TEX_W - 68), 34, 170);

    // Illustrative trend: soft area + line, always rising overall.
    const chartX = 34;
    const chartY = 204;
    const chartW = TEX_W - 68;
    const chartH = TEX_H - chartY - 40;
    const rand = mulberry32(index * 9973 + 17);
    const pts = [];
    const N = 9;
    for (let i = 0; i < N; i++) {
        const trend = i / (N - 1);
        const v = 0.15 + trend * 0.7 + (rand() - 0.5) * 0.18;
        pts.push([chartX + (i / (N - 1)) * chartW, chartY + chartH - Math.max(0.05, Math.min(0.98, v)) * chartH]);
    }
    ctx.strokeStyle = 'rgba(166, 176, 195, 0.12)';
    ctx.lineWidth = 1;
    for (let g = 0; g <= 3; g++) {
        const gy = chartY + (g / 3) * chartH;
        ctx.beginPath();
        ctx.moveTo(chartX, gy);
        ctx.lineTo(chartX + chartW, gy);
        ctx.stroke();
    }
    const path = () => {
        ctx.beginPath();
        ctx.moveTo(pts[0][0], pts[0][1]);
        for (let i = 1; i < pts.length; i++) {
            const [px, py] = pts[i - 1];
            const [cx, cy] = pts[i];
            const mx = (px + cx) / 2;
            ctx.bezierCurveTo(mx, py, mx, cy, cx, cy);
        }
    };
    path();
    ctx.lineTo(chartX + chartW, chartY + chartH);
    ctx.lineTo(chartX, chartY + chartH);
    ctx.closePath();
    const area = ctx.createLinearGradient(0, chartY, 0, chartY + chartH);
    area.addColorStop(0, hexA(accent, 0.38));
    area.addColorStop(1, hexA(accent, 0));
    ctx.fillStyle = area;
    ctx.fill();
    path();
    const line = ctx.createLinearGradient(chartX, 0, chartX + chartW, 0);
    line.addColorStop(0, accent);
    line.addColorStop(1, '#b18cff');
    ctx.strokeStyle = line;
    ctx.lineWidth = 5;
    ctx.lineCap = 'round';
    ctx.stroke();
    const [lx, ly] = pts[pts.length - 1];
    ctx.fillStyle = '#ffffff';
    ctx.beginPath();
    ctx.arc(lx, ly, 8, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = hexA(accent, 0.5);
    ctx.lineWidth = 6;
    ctx.beginPath();
    ctx.arc(lx, ly, 15, 0, Math.PI * 2);
    ctx.stroke();

    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

function makeCardBack() {
    const c = document.createElement('canvas');
    c.width = TEX_W;
    c.height = TEX_H;
    const ctx = c.getContext('2d');
    cardBase(ctx, 'rgba(109, 147, 255, 0.6)');
    ctx.strokeStyle = 'rgba(109, 147, 255, 0.08)';
    ctx.lineWidth = 2;
    for (let x = 40; x < TEX_W; x += 40) {
        ctx.beginPath();
        ctx.moveTo(x, 20);
        ctx.lineTo(x, TEX_H - 20);
        ctx.stroke();
    }
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

function makeBeamTexture() {
    const c = document.createElement('canvas');
    c.width = 4;
    c.height = 128;
    const ctx = c.getContext('2d');
    const g = ctx.createLinearGradient(0, 0, 0, 128);
    g.addColorStop(0, 'rgba(255,255,255,0)');
    g.addColorStop(1, 'rgba(255,255,255,0.9)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, 4, 128);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

function makeGlowTexture() {
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

/* ---------------------------------------------------------------------- */

function fit(ctx, text, max) {
    if (ctx.measureText(text).width <= max) return text;
    let s = text;
    while (s.length > 1 && ctx.measureText(s + '…').width > max) s = s.slice(0, -1);
    return s + '…';
}

function hexA(hex, a) {
    const n = parseInt(hex.slice(1), 16);
    return `rgba(${(n >> 16) & 255}, ${(n >> 8) & 255}, ${n & 255}, ${a})`;
}

function mulberry32(seed) {
    let a = seed >>> 0;
    return () => {
        a = (a + 0x6d2b79f5) >>> 0;
        let t = a;
        t = Math.imul(t ^ (t >>> 15), t | 1);
        t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
}

function easeOutCubic(x) {
    return 1 - Math.pow(1 - x, 3);
}

function easeOutBack(x) {
    const c1 = 1.4;
    const c3 = c1 + 1;
    return 1 + c3 * Math.pow(x - 1, 3) + c1 * Math.pow(x - 1, 2);
}
