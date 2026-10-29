/**
 * Services hero 3D scene: a full-funnel marketing engine.
 *
 * Meta, Google and TikTok nodes orbit above a glass funnel and feed it a
 * stream of "leads" (particles). Leads spiral down through the funnel's
 * stages; some drop out along the way, the rest reach the conversion core
 * at the bottom, which pulses as they land.
 *
 * Renders only while the hero is on screen and the tab is visible, at a
 * lighter quality on low-power devices. With reduced motion it draws one
 * still frame. Without WebGL the CSS tile fallback in the markup stays.
 */
import * as THREE from 'three';

const container = document.querySelector('[data-services-scene]');

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

if (container && wideEnough && webglAvailable()) whenIdle(init, { timeout: 800 });

const PLATFORMS = [
    { label: 'Meta Ads', color: '#5b8cff' },
    { label: 'Google Ads', color: '#3ddc97' },
    { label: 'TikTok Ads', color: '#ff5c8a' },
];
const CONVERT = new THREE.Color('#5ff0d8');
const DROPPED = new THREE.Color('#7a8499');

// Funnel shape: s runs 0 (top rim) → 1 (bottom neck).
const TOP_Y = 1.9;
const BOTTOM_Y = -1.75;
const TOP_R = 2.35;
const BOTTOM_R = 0.38;
const CORE_Y = -2.3;
const funnelY = (s) => TOP_Y + (BOTTOM_Y - TOP_Y) * s;
const funnelR = (s) => BOTTOM_R + (TOP_R - BOTTOM_R) * Math.pow(1 - s, 1.55);

function init() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const labelsEl = container.querySelector('[data-scene-labels]');

    const renderer = new THREE.WebGLRenderer({ antialias: !lowPower, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, lowPower ? 1 : 1.5));
    renderer.setClearColor(0x000000, 0);
    container.prepend(renderer.domElement);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 60);
    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };
    const glowTex = track(makeGlowTexture());

    // Everything that turns with the funnel lives in `world`.
    const world = new THREE.Group();
    scene.add(world);

    // ---- Floor: a faint polar grid under the funnel
    const grid = new THREE.PolarGridHelper(3.4, 16, 6, 96, 0x4f7cff, 0x4f7cff);
    grid.material.transparent = true;
    grid.material.opacity = 0.1;
    grid.material.depthWrite = false;
    grid.position.y = CORE_Y - 0.25;
    track(grid.geometry); track(grid.material);
    world.add(grid);

    // ---- Funnel glass: a lathe surface, rings for each stage, and vertical ribs
    const profile = [];
    for (let i = 0; i <= 32; i++) {
        const s = i / 32;
        profile.push(new THREE.Vector2(funnelR(s), funnelY(s)));
    }
    const glass = new THREE.Mesh(
        track(new THREE.LatheGeometry(profile, 72)),
        track(new THREE.MeshBasicMaterial({ color: 0x4f7cff, transparent: true, opacity: 0.055, side: THREE.DoubleSide, depthWrite: false, blending: THREE.AdditiveBlending })),
    );
    world.add(glass);

    const ringColors = [new THREE.Color('#6d93ff'), new THREE.Color('#8b5cf6'), new THREE.Color('#2dd4bf')];
    const colorAt = (s, out) => (s < 0.5 ? out.copy(ringColors[0]).lerp(ringColors[1], s * 2) : out.copy(ringColors[1]).lerp(ringColors[2], (s - 0.5) * 2));

    const rings = [];
    const RING_COUNT = 8;
    for (let i = 0; i < RING_COUNT; i++) {
        const s = i / (RING_COUNT - 1);
        const ring = new THREE.Mesh(
            track(new THREE.TorusGeometry(funnelR(s), i === 0 ? 0.018 : 0.011, 6, 140)),
            track(new THREE.MeshBasicMaterial({ color: colorAt(s, new THREE.Color()), transparent: true, opacity: i === 0 ? 0.95 : 0.6, blending: THREE.AdditiveBlending, depthWrite: false })),
        );
        ring.rotation.x = Math.PI / 2;
        ring.position.y = funnelY(s);
        ring.userData.s = s;
        world.add(ring);
        rings.push(ring);
    }

    const ribMat = track(new THREE.LineBasicMaterial({ color: 0x6d93ff, transparent: true, opacity: 0.16, blending: THREE.AdditiveBlending, depthWrite: false }));
    const RIBS = 18;
    for (let k = 0; k < RIBS; k++) {
        const a = (k / RIBS) * Math.PI * 2;
        const pts = profile.map((p) => new THREE.Vector3(Math.cos(a) * p.x, p.y, Math.sin(a) * p.x));
        world.add(new THREE.Line(track(new THREE.BufferGeometry().setFromPoints(pts)), ribMat));
    }

    // Light sparks that run around each ring.
    const sparkGeo = track(new THREE.SphereGeometry(0.035, 10, 10));
    const sparks = rings.map((ring, i) => {
        const spark = new THREE.Mesh(sparkGeo, track(new THREE.MeshBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.9 })));
        spark.userData = { r: funnelR(ring.userData.s), y: ring.position.y, speed: (i % 2 ? -1 : 1) * (0.5 + i * 0.07), phase: i * 1.3 };
        world.add(spark);
        return spark;
    });

    // ---- Conversion core
    const core = new THREE.Group();
    core.position.y = CORE_Y;
    world.add(core);
    const coreBall = new THREE.Mesh(
        track(new THREE.IcosahedronGeometry(0.17, 2)),
        track(new THREE.MeshBasicMaterial({ color: 0xbffcf1 })),
    );
    core.add(coreBall);
    const coreGlow = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color: 0x2dd4bf, transparent: true, opacity: 0.85, blending: THREE.AdditiveBlending, depthWrite: false })));
    coreGlow.scale.setScalar(1.4);
    core.add(coreGlow);
    const coreShell = new THREE.Mesh(
        track(new THREE.IcosahedronGeometry(0.34, 1)),
        track(new THREE.MeshBasicMaterial({ color: 0x5ff0d8, wireframe: true, transparent: true, opacity: 0.35 })),
    );
    core.add(coreShell);

    // Shockwave rings fired as leads convert.
    const waveGeo = track(new THREE.RingGeometry(0.3, 0.33, 64));
    const waves = Array.from({ length: 5 }, () => {
        const m = new THREE.Mesh(waveGeo, track(new THREE.MeshBasicMaterial({ color: 0x5ff0d8, transparent: true, opacity: 0, side: THREE.DoubleSide, blending: THREE.AdditiveBlending, depthWrite: false })));
        m.rotation.x = -Math.PI / 2;
        m.position.y = CORE_Y - 0.05;
        m.userData.age = 1;
        world.add(m);
        return m;
    });
    let nextWave = 0;
    let lastWaveAt = -1;
    const fireWave = (t) => {
        if (t - lastWaveAt < 0.45) return;
        lastWaveAt = t;
        waves[nextWave].userData.age = 0;
        nextWave = (nextWave + 1) % waves.length;
    };

    // ---- Platform nodes orbiting above the rim
    const NODE_R = 3.15;
    const NODE_Y = 2.75;
    const nodeGeo = track(new THREE.IcosahedronGeometry(0.15, 2));
    const haloGeo = track(new THREE.TorusGeometry(0.3, 0.01, 6, 64));
    const nodes = PLATFORMS.map((p, i) => {
        const color = new THREE.Color(p.color);
        const group = new THREE.Group();
        group.add(new THREE.Mesh(nodeGeo, track(new THREE.MeshBasicMaterial({ color: color.clone().lerp(new THREE.Color(0xffffff), 0.35) }))));
        const glow = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color, transparent: true, opacity: 0.9, blending: THREE.AdditiveBlending, depthWrite: false })));
        glow.scale.setScalar(1.1);
        group.add(glow);
        const halo = new THREE.Mesh(haloGeo, track(new THREE.MeshBasicMaterial({ color, transparent: true, opacity: 0.7, blending: THREE.AdditiveBlending, depthWrite: false })));
        group.add(halo);
        world.add(group);

        const label = document.createElement('span');
        label.className = 'scene-label scene-label--platform';
        label.style.setProperty('--dot', p.color);
        label.textContent = p.label;
        labelsEl.appendChild(label);

        return { group, halo, color, base: (i / PLATFORMS.length) * Math.PI * 2, label };
    });

    // Feed lines from each node down to the rim (rebuilt as the nodes move).
    const feedMats = nodes.map((n) => track(new THREE.LineBasicMaterial({ color: n.color, transparent: true, opacity: 0.28, blending: THREE.AdditiveBlending, depthWrite: false })));
    const FEED_SEGS = 24;
    const feeds = nodes.map((n, i) => {
        const geo = track(new THREE.BufferGeometry());
        geo.setAttribute('position', new THREE.BufferAttribute(new Float32Array((FEED_SEGS + 1) * 3), 3));
        const line = new THREE.Line(geo, feedMats[i]);
        world.add(line);
        return line;
    });

    // ---- Stage labels (fixed to the screen side of the funnel, not rotating)
    const stages = [
        { label: 'Reach', s: 0.04 },
        { label: 'Engage', s: 0.45 },
        { label: 'Convert', s: 1.2 },
    ].map((st) => {
        const el = document.createElement('span');
        el.className = 'scene-label scene-label--stage';
        el.textContent = st.label;
        labelsEl.appendChild(el);
        const y = st.s > 1 ? CORE_Y : funnelY(st.s);
        const x = (st.s > 1 ? 0.55 : funnelR(st.s)) + 0.35;
        return { el, anchor: new THREE.Vector3(x, y, 0) };
    });

    // ---- Leads: one point cloud, positions driven on the CPU
    const COUNT = lowPower ? 420 : 950;
    const positions = new Float32Array(COUNT * 3);
    const colors = new Float32Array(COUNT * 3);
    const alphas = new Float32Array(COUNT);
    const sizes = new Float32Array(COUNT);
    const leads = [];
    const tmpColor = new THREE.Color();
    const seed = (lead, initial) => {
        lead.src = Math.floor(Math.random() * PLATFORMS.length);
        lead.a0 = Math.random() * Math.PI * 2;
        lead.k = 0.72 + Math.random() * 0.26;
        lead.speed = 0.075 + Math.random() * 0.05;
        lead.turns = 1.6 + Math.random() * 1.2;
        lead.drop = Math.random() < 0.55 ? 0.22 + Math.random() * 0.6 : 2; // 2 = converts
        lead.p = initial ? Math.random() : 0;
    };
    for (let i = 0; i < COUNT; i++) {
        const lead = {};
        seed(lead, true);
        leads.push(lead);
        sizes[i] = 0.07 + Math.random() * 0.09;
    }
    const leadGeo = track(new THREE.BufferGeometry());
    leadGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3).setUsage(THREE.DynamicDrawUsage));
    leadGeo.setAttribute('aColor', new THREE.BufferAttribute(colors, 3).setUsage(THREE.DynamicDrawUsage));
    leadGeo.setAttribute('aAlpha', new THREE.BufferAttribute(alphas, 1).setUsage(THREE.DynamicDrawUsage));
    leadGeo.setAttribute('aSize', new THREE.BufferAttribute(sizes, 1));
    leadGeo.boundingSphere = new THREE.Sphere(new THREE.Vector3(), 8);
    const leadMat = track(new THREE.ShaderMaterial({
        uniforms: { uScale: { value: 400 } },
        vertexShader: `
            attribute vec3 aColor;
            attribute float aAlpha;
            attribute float aSize;
            uniform float uScale;
            varying vec3 vColor;
            varying float vAlpha;
            void main() {
                vColor = aColor;
                vAlpha = aAlpha;
                vec4 mv = modelViewMatrix * vec4(position, 1.0);
                gl_PointSize = aSize * uScale / -mv.z;
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: `
            varying vec3 vColor;
            varying float vAlpha;
            void main() {
                float d = length(gl_PointCoord - 0.5);
                float a = smoothstep(0.5, 0.0, d);
                gl_FragColor = vec4(vColor, a * a * vAlpha);
            }`,
        transparent: true,
        depthWrite: false,
        blending: THREE.AdditiveBlending,
    }));
    world.add(new THREE.Points(leadGeo, leadMat));

    const nodePos = nodes.map(() => new THREE.Vector3());
    const rimPoint = new THREE.Vector3();
    const ctrl = new THREE.Vector3();

    function updateLeads(t, dt, fade) {
        for (let i = 0; i < COUNT; i++) {
            const lead = leads[i];
            lead.p += lead.speed * dt;
            if (lead.p >= 1) {
                if (lead.drop > 1) fireWave(t);
                seed(lead, false);
            }
            const p = lead.p;
            const src = nodes[lead.src];
            let x; let y; let z; let alpha = 0.95;
            const i3 = i * 3;

            if (p < 0.18) {
                // From the platform node, arcing over into the rim.
                const q = easeInOut(p / 0.18);
                const from = nodePos[lead.src];
                rimPoint.set(Math.cos(lead.a0) * TOP_R * lead.k, TOP_Y, Math.sin(lead.a0) * TOP_R * lead.k);
                ctrl.copy(from).add(rimPoint).multiplyScalar(0.5);
                ctrl.y += 0.9;
                const u = 1 - q;
                x = u * u * from.x + 2 * u * q * ctrl.x + q * q * rimPoint.x;
                y = u * u * from.y + 2 * u * q * ctrl.y + q * q * rimPoint.y;
                z = u * u * from.z + 2 * u * q * ctrl.z + q * q * rimPoint.z;
                alpha = Math.min(1, q * 3) * 0.9;
                tmpColor.copy(src.color);
            } else {
                const s = (p - 0.18) / 0.82;
                const dropped = s > lead.drop;
                const sAt = dropped ? lead.drop : s;
                const angle = lead.a0 + sAt * lead.turns * Math.PI * 2;
                const r = funnelR(sAt) * lead.k;
                x = Math.cos(angle) * r;
                z = Math.sin(angle) * r;
                y = funnelY(sAt);
                tmpColor.copy(src.color).lerp(CONVERT, Math.min(1, sAt * 1.15));
                if (dropped) {
                    // Filtered out: drifts outward through the glass and fades.
                    const d = Math.min(1, (s - lead.drop) / 0.22);
                    const push = 1 + d * 0.9;
                    x *= push;
                    z *= push;
                    y -= d * 0.35;
                    alpha = (1 - d) * 0.75;
                    tmpColor.lerp(DROPPED, d);
                } else if (s > 0.94) {
                    // Into the core.
                    const d = (s - 0.94) / 0.06;
                    x *= 1 - d;
                    z *= 1 - d;
                    y = THREE.MathUtils.lerp(BOTTOM_Y, CORE_Y, d);
                    alpha = 1 - d * 0.6;
                }
            }
            positions[i3] = x;
            positions[i3 + 1] = y;
            positions[i3 + 2] = z;
            colors[i3] = tmpColor.r;
            colors[i3 + 1] = tmpColor.g;
            colors[i3 + 2] = tmpColor.b;
            alphas[i] = alpha * fade;
        }
        leadGeo.attributes.position.needsUpdate = true;
        leadGeo.attributes.aColor.needsUpdate = true;
        leadGeo.attributes.aAlpha.needsUpdate = true;
    }

    // ---- Layout: fit the whole rig in the container
    let width = 1;
    let height = 1;
    function layout() {
        width = container.clientWidth || 1;
        height = container.clientHeight || 1;
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        const halfV = Math.tan(THREE.MathUtils.degToRad(camera.fov / 2));
        const needH = 3.8 / halfV;
        const needW = 4.0 / (halfV * camera.aspect);
        const dist = Math.max(needH, needW) * 1.02;
        camera.position.set(0, dist * 0.2, dist);
        camera.lookAt(0, 0.55, 0);
        camera.updateProjectionMatrix();
        // World units → pixels for the point sprites.
        leadMat.uniforms.uScale.value = (height * renderer.getPixelRatio()) / (2 * halfV);
    }
    layout();

    // ---- Pointer parallax
    const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
    const onMove = (e) => {
        pointer.tx = e.clientX / window.innerWidth - 0.5;
        pointer.ty = e.clientY / window.innerHeight - 0.5;
    };
    if (!reduced) window.addEventListener('pointermove', onMove, { passive: true });

    // ---- HTML labels follow their 3D anchors
    const v = new THREE.Vector3();
    function placeLabel(el, worldPos, alpha) {
        v.copy(worldPos).project(camera);
        const x = (v.x * 0.5 + 0.5) * width;
        const y = (-v.y * 0.5 + 0.5) * height;
        el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0) translate(-50%, -50%)`;
        el.style.opacity = alpha.toFixed(2);
    }

    // ---- Frame
    const heroH = container.closest('.page-hero')?.offsetHeight || 700;
    const clock = new THREE.Clock(false);
    let t = 0;
    let intro = 0;
    let running = false;
    let inView = true;
    let frame = 0;
    let slowFrames = 0;
    let downgraded = false;

    function renderFrame(dt) {
        pointer.x += (pointer.tx - pointer.x) * 0.05;
        pointer.y += (pointer.ty - pointer.y) * 0.05;
        const scroll = Math.min(window.scrollY / heroH, 1);
        const ease = easeOutCubic(Math.min(1, intro));

        world.rotation.y = t * 0.11 + pointer.x * 0.35 + scroll * 0.6;
        world.rotation.x = 0.04 + pointer.y * 0.08;
        world.position.y = -scroll * 0.9;
        world.updateMatrixWorld();

        // Rings draw in from the top, one after another.
        rings.forEach((ring, i) => {
            const k = easeOutCubic(THREE.MathUtils.clamp(intro * 2.2 - i * 0.12, 0, 1));
            ring.scale.setScalar(Math.max(0.001, k));
            ring.material.opacity = (i === 0 ? 0.95 : 0.55) * k * (0.85 + Math.sin(t * 1.6 - i * 0.7) * 0.15);
        });
        glass.material.opacity = 0.055 * ease;
        ribMat.opacity = 0.16 * ease;

        sparks.forEach((spark) => {
            const { r, y, speed, phase } = spark.userData;
            const a = t * speed + phase;
            spark.position.set(Math.cos(a) * r, y, Math.sin(a) * r);
            spark.material.opacity = 0.9 * ease;
        });

        nodes.forEach((n, i) => {
            const a = n.base + t * 0.16;
            const bob = Math.sin(t * 1.3 + i * 2) * 0.12;
            n.group.position.set(Math.cos(a) * NODE_R, NODE_Y + bob, Math.sin(a) * NODE_R);
            n.group.scale.setScalar(Math.max(0.001, ease));
            n.halo.rotation.set(t * 0.9 + i, t * 0.6, 0);
            nodePos[i].copy(n.group.position);

            // Feed line: node → rim, bowing upward.
            const attr = feeds[i].geometry.attributes.position;
            const rimA = a + 0.35;
            rimPoint.set(Math.cos(rimA) * TOP_R, TOP_Y, Math.sin(rimA) * TOP_R);
            ctrl.copy(nodePos[i]).add(rimPoint).multiplyScalar(0.5);
            ctrl.y += 0.7;
            for (let k = 0; k <= FEED_SEGS; k++) {
                const q = k / FEED_SEGS;
                const u = 1 - q;
                attr.setXYZ(k,
                    u * u * nodePos[i].x + 2 * u * q * ctrl.x + q * q * rimPoint.x,
                    u * u * nodePos[i].y + 2 * u * q * ctrl.y + q * q * rimPoint.y,
                    u * u * nodePos[i].z + 2 * u * q * ctrl.z + q * q * rimPoint.z);
            }
            attr.needsUpdate = true;
            feedMats[i].opacity = 0.28 * ease;

            // Labels sit just above their node; dimmer when the node is behind the funnel.
            v.copy(n.group.position).applyMatrix4(world.matrixWorld);
            const depth = THREE.MathUtils.clamp((v.z + NODE_R) / (2 * NODE_R), 0, 1);
            v.y += 0.42;
            placeLabel(n.label, v, ease * (0.35 + depth * 0.65));
        });

        stages.forEach((st) => {
            v.copy(st.anchor);
            v.y += world.position.y;
            placeLabel(st.el, v, ease);
        });

        // Core breathes; shockwaves expand and fade.
        const beat = 1 + Math.sin(t * 3) * 0.06;
        coreBall.scale.setScalar(beat * Math.max(0.001, ease));
        coreGlow.scale.setScalar(1.4 * beat * ease);
        coreShell.rotation.set(t * 0.4, t * 0.6, 0);
        coreShell.scale.setScalar(Math.max(0.001, ease));
        waves.forEach((w) => {
            if (w.userData.age >= 1) { w.material.opacity = 0; return; }
            w.userData.age = Math.min(1, w.userData.age + dt * 0.7);
            const a = w.userData.age;
            w.scale.setScalar(1 + a * 7);
            w.material.opacity = (1 - a) * 0.55;
        });

        updateLeads(t, dt, THREE.MathUtils.clamp(intro * 1.6 - 0.6, 0, 1));
        renderer.render(scene, camera);
    }

    function loop() {
        if (!running) return;
        const dt = Math.min(clock.getDelta(), 1 / 30);
        t += dt;
        intro = Math.min(1, intro + dt / 1.8);
        if (!downgraded && dt > 1 / 40) {
            if (++slowFrames > 45) {
                downgraded = true;
                renderer.setPixelRatio(1);
                layout();
            }
        } else if (slowFrames > 0) {
            slowFrames--;
        }
        renderFrame(dt);
        frame = requestAnimationFrame(loop);
    }

    function renderStill() {
        t = 4;
        intro = 1;
        for (let i = 0; i < 4; i++) updateLeads(t, 0, 1);
        renderFrame(0);
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

function easeOutCubic(x) {
    return 1 - Math.pow(1 - x, 3);
}

function easeInOut(x) {
    return x < 0.5 ? 2 * x * x : 1 - Math.pow(-2 * x + 2, 2) / 2;
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
