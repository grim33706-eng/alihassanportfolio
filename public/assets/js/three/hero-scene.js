/**
 * Home hero 3D scene: a "campaign growth" chart of glass bars, a trend line
 * with a travelling data pulse, orbit rings and a light particle field.
 *
 * Renders only while the hero is on screen and the tab is visible. With
 * reduced motion it draws a single still frame. Without WebGL the CSS
 * fallback in the markup stays visible.
 */
import * as THREE from 'three';

const container = document.querySelector('[data-hero-scene]');

function webglAvailable() {
    try {
        const c = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl')));
    } catch (e) {
        return false;
    }
}

if (container && webglAvailable()) init();

function init() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isSmall = () => window.innerWidth < 992;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
    renderer.setClearColor(0x000000, 0);
    container.appendChild(renderer.domElement);

    const scene = new THREE.Scene();
    scene.fog = new THREE.Fog(0x0b1020, 12, 26);

    const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 100);
    camera.position.set(0, 3.2, 13);

    // ---- Lights
    scene.add(new THREE.AmbientLight(0x8fa6ff, 0.55));
    const blue = new THREE.PointLight(0x4f7cff, 60, 30);
    blue.position.set(-4, 6, 6);
    scene.add(blue);
    const violet = new THREE.PointLight(0x8b5cf6, 50, 30);
    violet.position.set(6, 3, -2);
    scene.add(violet);

    const world = new THREE.Group();
    scene.add(world);
    const chart = new THREE.Group();
    world.add(chart);

    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };

    // ---- Bars: 3 rows × 7 columns, rising left to right
    const cols = 7;
    const rows = 3;
    const gap = 0.95;
    const barGeo = track(new THREE.BoxGeometry(0.58, 1, 0.58));
    barGeo.translate(0, 0.5, 0); // grow from the base
    const edgeGeo = track(new THREE.EdgesGeometry(barGeo));

    const bars = [];
    const tops = [];
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            const t = c / (cols - 1);
            const height = 0.5 + Math.pow(t, 1.6) * 4.2 * (1 - r * 0.18) + Math.sin(c * 1.7 + r) * 0.25;
            const color = new THREE.Color().lerpColors(new THREE.Color(0x4f7cff), new THREE.Color(0x8b5cf6), t);

            const mat = track(new THREE.MeshStandardMaterial({
                color,
                emissive: color,
                emissiveIntensity: 0.25 + t * 0.35,
                metalness: 0.2,
                roughness: 0.35,
                transparent: true,
                opacity: 0.32 + (rows - r) * 0.1,
            }));
            const mesh = new THREE.Mesh(barGeo, mat);
            const x = (c - (cols - 1) / 2) * gap;
            const z = (r - (rows - 1) / 2) * gap * 1.1;
            mesh.position.set(x, 0, z);

            const edgeMat = track(new THREE.LineBasicMaterial({ color: color.clone().offsetHSL(0, 0, 0.18), transparent: true, opacity: 0.85 - r * 0.2 }));
            mesh.add(new THREE.LineSegments(edgeGeo, edgeMat));

            mesh.scale.y = 0.001;
            mesh.userData = { height, phase: Math.random() * Math.PI * 2 };
            chart.add(mesh);
            bars.push(mesh);
            if (r === 0) tops.push(new THREE.Vector3(x, height + 0.35, z));
        }
    }

    // ---- Trend line through the front-row tops
    const curve = new THREE.CatmullRomCurve3(tops, false, 'centripetal');
    const tubeGeo = track(new THREE.TubeGeometry(curve, 160, 0.028, 8, false));
    const tubeMat = track(new THREE.MeshBasicMaterial({ color: 0xb9c8ff, transparent: true, opacity: 0.95 }));
    const tube = new THREE.Mesh(tubeGeo, tubeMat);
    tubeGeo.setDrawRange(0, 0);
    chart.add(tube);

    // Nodes on the line
    const nodeGeo = track(new THREE.SphereGeometry(0.075, 16, 16));
    const nodeMat = track(new THREE.MeshBasicMaterial({ color: 0xffffff }));
    const nodes = tops.map((p) => {
        const n = new THREE.Mesh(nodeGeo, nodeMat);
        n.position.copy(p);
        n.scale.setScalar(0.001);
        chart.add(n);
        return n;
    });

    // Travelling pulse with a soft glow sprite
    const glowTex = track(makeGlowTexture());
    const pulseMat = track(new THREE.SpriteMaterial({ map: glowTex, color: 0x9db3ff, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending }));
    const pulse = new THREE.Sprite(pulseMat);
    pulse.scale.setScalar(0.9);
    chart.add(pulse);

    // ---- Base grid
    const grid = new THREE.GridHelper(14, 28, 0x293247, 0x1a2135);
    grid.material.transparent = true;
    grid.material.opacity = 0.55;
    track(grid.geometry); track(grid.material);
    grid.position.y = -0.01;
    world.add(grid);

    // ---- Orbit rings (targeting)
    const ringGeo = track(new THREE.TorusGeometry(4.6, 0.012, 8, 160));
    const ringMat1 = track(new THREE.MeshBasicMaterial({ color: 0x4f7cff, transparent: true, opacity: 0.45 }));
    const ringMat2 = track(new THREE.MeshBasicMaterial({ color: 0x8b5cf6, transparent: true, opacity: 0.35 }));
    const ring1 = new THREE.Mesh(ringGeo, ringMat1);
    const ring2 = new THREE.Mesh(ringGeo, ringMat2);
    ring1.rotation.set(Math.PI / 2.3, 0.2, 0);
    ring2.rotation.set(Math.PI / 1.8, -0.5, 0.3);
    ring2.scale.setScalar(1.12);
    ring1.position.y = ring2.position.y = 2;
    world.add(ring1, ring2);

    // Satellites on the rings
    const satGeo = track(new THREE.SphereGeometry(0.09, 12, 12));
    const sat1 = new THREE.Mesh(satGeo, track(new THREE.MeshBasicMaterial({ color: 0x9db3ff })));
    const sat2 = new THREE.Mesh(satGeo, track(new THREE.MeshBasicMaterial({ color: 0xc4a8ff })));
    ring1.add(sat1);
    ring2.add(sat2);

    // ---- Particles
    const count = isSmall() ? 260 : 600;
    const positions = new Float32Array(count * 3);
    for (let i = 0; i < count; i++) {
        positions[i * 3] = (Math.random() - 0.5) * 22;
        positions[i * 3 + 1] = Math.random() * 10 - 1;
        positions[i * 3 + 2] = (Math.random() - 0.5) * 14;
    }
    const pGeo = track(new THREE.BufferGeometry());
    pGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    const pMat = track(new THREE.PointsMaterial({ size: 0.05, map: glowTex, color: 0xa8b8ff, transparent: true, opacity: 0.7, depthWrite: false, blending: THREE.AdditiveBlending }));
    const particles = new THREE.Points(pGeo, pMat);
    scene.add(particles);

    // ---- Layout: chart sits to the right on desktop, centred and lower on mobile
    let heroH = 1; // cached: measuring the DOM inside the render loop forces layout every frame
    function layout() {
        const w = container.clientWidth;
        const h = container.clientHeight;
        heroH = h || 1;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();

        if (isSmall()) {
            world.position.set(0, -0.6, -2.5);
            world.scale.setScalar(0.85);
            camera.lookAt(0, 2.4, 0);
        } else {
            world.position.set(w / h > 1.9 ? 4.6 : 3.6, -0.8, 0);
            world.scale.setScalar(1);
            camera.lookAt(0, 1.8, 0);
        }
    }
    layout();

    // ---- Intro: grow the bars, draw the line
    const intro = { p: reduced ? 1 : 0 };
    const tubeCount = tubeGeo.index.count;
    // Bars are scaled in the render loop; this drives the line and nodes.
    function applyIntro() {
        const lineP = THREE.MathUtils.clamp((intro.p - 0.45) / 0.55, 0, 1);
        tubeGeo.setDrawRange(0, Math.floor(tubeCount * lineP / 3) * 3);
        nodes.forEach((n, i) => {
            const nodeP = THREE.MathUtils.clamp((lineP * cols - i) * 1.5, 0, 1);
            n.scale.setScalar(Math.max(0.001, nodeP));
        });
    }
    if (!reduced && window.gsap) {
        gsap.to(intro, { p: 1, duration: 2.6, ease: 'power2.out', delay: 0.3, onUpdate: applyIntro });
    } else {
        intro.p = 1;
    }
    applyIntro();

    // ---- Interaction: pointer parallax + scroll
    const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
    const onPointer = (e) => {
        pointer.tx = (e.clientX / window.innerWidth - 0.5) * 2;
        pointer.ty = (e.clientY / window.innerHeight - 0.5) * 2;
    };
    if (!reduced) window.addEventListener('pointermove', onPointer, { passive: true });

    // ---- Render loop (only while visible)
    const clock = new THREE.Clock(false);
    let running = false;
    let inView = true;
    let frame = 0;
    // Scene time advances in capped steps, so a slow frame or a return from off-screen
    // continues smoothly instead of jumping ahead.
    let t = 0;
    // Adaptive quality: if frames run slow, drop to 1x pixel ratio once.
    let slowFrames = 0;
    let downgraded = false;

    // Per-bar growth factor (0..1, plus a little breathing once grown).
    const baseScale = bars.map(() => 1);

    function renderStill() {
        bars.forEach((b) => b.scale.set(1, b.userData.height, 1));
        pulseMat.opacity = 0.9;
        curve.getPointAt(0.85, pulse.position);
        renderer.render(scene, camera);
    }

    function loop() {
        if (!running) return;
        const dt = clock.getDelta();
        t += Math.min(dt, 1 / 30);
        if (!downgraded && dt > 1 / 40) {
            if (++slowFrames > 90) {
                downgraded = true;
                renderer.setPixelRatio(1);
                layout();
            }
        } else if (slowFrames > 0) {
            slowFrames--;
        }
        bars.forEach((b, i) => {
            const col = i % cols;
            const grow = easeOutCubic(THREE.MathUtils.clamp(intro.p * 1.6 - col * 0.08, 0, 1));
            const breathe = intro.p >= 1 ? 1 + Math.sin(t * 1.2 + b.userData.phase) * 0.035 : 1;
            baseScale[i] = Math.max(0.001, grow * breathe);
        });
        renderFrame(t);
        frame = requestAnimationFrame(loop);
    }

    function renderFrame(t) {
        pointer.x += (pointer.tx - pointer.x) * 0.04;
        pointer.y += (pointer.ty - pointer.y) * 0.04;
        const scroll = Math.min(window.scrollY / heroH, 1);

        world.rotation.y = -0.45 + pointer.x * 0.18 + t * 0.03 + scroll * 0.5;
        world.rotation.x = pointer.y * 0.05;
        chart.position.y = scroll * -1.2;

        bars.forEach((b, i) => b.scale.set(1, b.userData.height * baseScale[i], 1));

        curve.getPointAt((t * 0.12) % 1, pulse.position);
        pulseMat.opacity = intro.p >= 1 ? 0.9 : 0;

        ring1.rotation.z = t * 0.12;
        ring2.rotation.z = -t * 0.09;
        sat1.position.set(Math.cos(t * 0.6) * 4.6, Math.sin(t * 0.6) * 4.6, 0);
        sat2.position.set(Math.cos(-t * 0.45 + 2) * 4.6, Math.sin(-t * 0.45 + 2) * 4.6, 0);

        particles.rotation.y = t * 0.015;
        particles.position.y = Math.sin(t * 0.3) * 0.15;

        renderer.render(scene, camera);
    }

    function start() {
        if (running || reduced) return;
        running = true;
        clock.start(); // resets the delta only; scene time `t` carries on
        frame = requestAnimationFrame(loop);
    }
    function stop() {
        running = false;
        cancelAnimationFrame(frame);
    }

    // Compile shaders now, while the page loader still covers the screen,
    // rather than stalling on the first visible frame.
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
    if (reduced) {
        renderStill();
    } else {
        start();
    }

    // ---- Cleanup when leaving the page
    window.App?.onTeardown(() => {
        stop();
        io.disconnect();
        window.removeEventListener('pointermove', onPointer);
        window.removeEventListener('resize', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        disposables.forEach((d) => d.dispose && d.dispose());
        renderer.dispose();
        renderer.forceContextLoss();
    });
}

function easeOutCubic(x) {
    return 1 - Math.pow(1 - x, 3);
}

function makeGlowTexture() {
    const size = 64;
    const c = document.createElement('canvas');
    c.width = c.height = size;
    const ctx = c.getContext('2d');
    const g = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
    g.addColorStop(0, 'rgba(255,255,255,1)');
    g.addColorStop(0.25, 'rgba(255,255,255,0.6)');
    g.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, size, size);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}
