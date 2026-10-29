/**
 * Results hero 3D scene: a growth chart in space. A glowing trend line draws
 * itself over a translucent area slab, light columns rise from a waving dot
 * landscape, particles drift upward, and the peak pulses. The two pins
 * ("average" and "peak", editable in the admin) follow their points on the
 * curve. Illustrative shape, not a data plot.
 *
 * Renders only while the hero is on screen and the tab is visible, at a
 * lighter quality on low-power devices. With reduced motion it draws one
 * still frame. Without WebGL the SVG graph in the markup stays.
 */
import * as THREE from 'three';

const container = document.querySelector('[data-results-scene]');

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

const FLOOR_Y = -1.45;
const DEPTH = 0.75; // thickness of the area slab

// The same curve as the 2D fallback graph (SVG viewBox 600 × 300), mapped into the scene.
const SVG_POINTS = [[0, 260], [80, 250], [190, 210], [300, 165], [360, 140], [480, 80], [600, 40]];
const toScene = ([x, y]) => new THREE.Vector3(-3.3 + (x / 600) * 6.6, -1.3 + ((300 - y) / 300) * 4.1, 0);

function init() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const labelsEl = container.querySelector('[data-scene-labels]');

    const renderer = new THREE.WebGLRenderer({ antialias: !lowPower, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, lowPower ? 1 : 1.5));
    renderer.setClearColor(0x000000, 0);
    container.prepend(renderer.domElement);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 60);
    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };
    const glowTex = track(makeGlowTexture());

    const world = new THREE.Group();
    scene.add(world);
    const chart = new THREE.Group();
    world.add(chart);

    const curve = new THREE.CatmullRomCurve3(SVG_POINTS.map(toScene), false, 'centripetal');
    const SAMPLES = 120;
    const samples = curve.getSpacedPoints(SAMPLES);

    // ---- Area slab: front wall, back wall and top skin, fading toward the floor
    const fadeTex = track(makeFadeTexture());
    const wallMat = (opacity) => track(new THREE.MeshBasicMaterial({ map: fadeTex, color: 0x4f7cff, transparent: true, opacity, side: THREE.DoubleSide, depthWrite: false, blending: THREE.AdditiveBlending }));
    const wall = (z, mat) => {
        const pos = [];
        const uv = [];
        const idx = [];
        samples.forEach((p, i) => {
            pos.push(p.x, p.y, z, p.x, FLOOR_Y, z);
            uv.push(i / SAMPLES, 1, i / SAMPLES, 0);
            if (i < SAMPLES) {
                const a = i * 2;
                idx.push(a, a + 1, a + 2, a + 1, a + 3, a + 2);
            }
        });
        const g = track(new THREE.BufferGeometry());
        g.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
        g.setAttribute('uv', new THREE.Float32BufferAttribute(uv, 2));
        g.setIndex(idx);
        const m = new THREE.Mesh(g, mat);
        chart.add(m);
        return m;
    };
    const front = wall(0, wallMat(0.32));
    const back = wall(-DEPTH, wallMat(0.22));
    {
        const pos = [];
        const idx = [];
        samples.forEach((p, i) => {
            pos.push(p.x, p.y, 0, p.x, p.y, -DEPTH);
            if (i < SAMPLES) {
                const a = i * 2;
                idx.push(a, a + 1, a + 2, a + 1, a + 3, a + 2);
            }
        });
        const g = track(new THREE.BufferGeometry());
        g.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
        g.setIndex(idx);
        chart.add(new THREE.Mesh(g, track(new THREE.MeshBasicMaterial({ color: 0x8b5cf6, transparent: true, opacity: 0.22, side: THREE.DoubleSide, depthWrite: false, blending: THREE.AdditiveBlending }))));
    }
    const backLine = new THREE.Line(
        track(new THREE.BufferGeometry().setFromPoints(samples.map((p) => new THREE.Vector3(p.x, p.y, -DEPTH)))),
        track(new THREE.LineBasicMaterial({ color: 0x8b5cf6, transparent: true, opacity: 0.6, blending: THREE.AdditiveBlending, depthWrite: false })),
    );
    chart.add(backLine);

    // ---- Trend line: a tube that draws itself in, with a gradient from blue to violet
    const tubeGeo = track(new THREE.TubeGeometry(curve, 240, 0.035, 10, false));
    {
        const colors = [];
        const pos = tubeGeo.attributes.position;
        const c = new THREE.Color();
        for (let i = 0; i < pos.count; i++) {
            const u = THREE.MathUtils.clamp((pos.getX(i) + 3.3) / 6.6, 0, 1);
            c.set('#7aa2ff').lerp(new THREE.Color('#c4a8ff'), u);
            colors.push(c.r, c.g, c.b);
        }
        tubeGeo.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));
    }
    const tube = new THREE.Mesh(tubeGeo, track(new THREE.MeshBasicMaterial({ vertexColors: true })));
    chart.add(tube);
    const tubeCount = tubeGeo.index.count;
    const tubeGlow = new THREE.Mesh(
        track(new THREE.TubeGeometry(curve, 160, 0.12, 8, false)),
        track(new THREE.MeshBasicMaterial({ color: 0x6d93ff, transparent: true, opacity: 0.16, blending: THREE.AdditiveBlending, depthWrite: false })),
    );
    chart.add(tubeGlow);
    const glowCount = tubeGlow.geometry.index.count;

    // Travelling pulse along the line
    const pulse = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color: 0xffffff, transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false })));
    pulse.scale.setScalar(0.55);
    chart.add(pulse);

    // ---- Light columns from the floor up to the curve
    const COLS = 15;
    const colGroup = new THREE.Group();
    chart.add(colGroup);
    const colGeo = track(new THREE.PlaneGeometry(0.12, 1));
    const colTex = track(makeFadeTexture());
    const columns = [];
    const dotGeo = track(new THREE.SphereGeometry(0.045, 10, 10));
    const dotMat = track(new THREE.MeshBasicMaterial({ color: 0xdfe6ff }));
    for (let i = 0; i < COLS; i++) {
        const u = (i + 0.5) / COLS;
        const p = curve.getPointAt(u);
        const h = p.y - FLOOR_Y;
        const mat = track(new THREE.MeshBasicMaterial({ map: colTex, color: new THREE.Color('#4f7cff').lerp(new THREE.Color('#8b5cf6'), u), transparent: true, opacity: 0.55, depthWrite: false, blending: THREE.AdditiveBlending, side: THREE.DoubleSide }));
        const col = new THREE.Mesh(colGeo, mat);
        col.position.set(p.x, FLOOR_Y + h / 2, -DEPTH / 2);
        col.scale.y = h;
        colGroup.add(col);
        const dot = new THREE.Mesh(dotGeo, dotMat);
        dot.position.set(p.x, p.y, 0);
        colGroup.add(dot);
        columns.push({ col, dot, h, u });
    }

    // ---- Dot landscape below, gently waving
    const GX = lowPower ? 44 : 64;
    const GZ = lowPower ? 18 : 26;
    const landPos = new Float32Array(GX * GZ * 3);
    const landBase = [];
    for (let z = 0; z < GZ; z++) {
        for (let x = 0; x < GX; x++) {
            const px = -4.6 + (x / (GX - 1)) * 9.2;
            const pz = -3.4 + (z / (GZ - 1)) * 5.2;
            landBase.push(px, pz);
        }
    }
    const landGeo = track(new THREE.BufferGeometry());
    landGeo.setAttribute('position', new THREE.BufferAttribute(landPos, 3).setUsage(THREE.DynamicDrawUsage));
    const landMat = track(new THREE.PointsMaterial({ size: 0.06, map: glowTex, color: 0x6d93ff, transparent: true, opacity: 0.7, depthWrite: false, blending: THREE.AdditiveBlending }));
    const land = new THREE.Points(landGeo, landMat);
    land.position.y = FLOOR_Y - 0.08;
    world.add(land);
    const waveLand = (t) => {
        for (let i = 0, n = GX * GZ; i < n; i++) {
            const x = landBase[i * 2];
            const z = landBase[i * 2 + 1];
            landPos[i * 3] = x;
            landPos[i * 3 + 1] = Math.sin(x * 0.9 + t * 0.8) * 0.07 + Math.cos(z * 1.3 + t * 0.6) * 0.06;
            landPos[i * 3 + 2] = z;
        }
        landGeo.attributes.position.needsUpdate = true;
    };

    // ---- Rising particles inside the slab
    const RISE = lowPower ? 90 : 200;
    const risePos = new Float32Array(RISE * 3);
    const rise = [];
    for (let i = 0; i < RISE; i++) {
        const u = Math.random();
        rise.push({ u, y: Math.random(), speed: 0.08 + Math.random() * 0.14, z: -Math.random() * DEPTH });
    }
    const riseGeo = track(new THREE.BufferGeometry());
    riseGeo.setAttribute('position', new THREE.BufferAttribute(risePos, 3).setUsage(THREE.DynamicDrawUsage));
    const riseMat = track(new THREE.PointsMaterial({ size: 0.07, map: glowTex, color: 0xb9c8ff, transparent: true, opacity: 0.8, depthWrite: false, blending: THREE.AdditiveBlending }));
    chart.add(new THREE.Points(riseGeo, riseMat));
    const riseTmp = new THREE.Vector3();
    const updateRise = (dt) => {
        for (let i = 0; i < RISE; i++) {
            const r = rise[i];
            r.y += r.speed * dt;
            if (r.y > 1) { r.y = 0; r.u = Math.random(); }
            curve.getPointAt(r.u, riseTmp);
            risePos[i * 3] = riseTmp.x;
            risePos[i * 3 + 1] = FLOOR_Y + (riseTmp.y - FLOOR_Y) * r.y;
            risePos[i * 3 + 2] = r.z;
        }
        riseGeo.attributes.position.needsUpdate = true;
    };

    // ---- Peak marker
    const peakPoint = curve.getPointAt(1);
    const peak = new THREE.Group();
    peak.position.copy(peakPoint);
    chart.add(peak);
    peak.add(new THREE.Mesh(track(new THREE.IcosahedronGeometry(0.11, 2)), track(new THREE.MeshBasicMaterial({ color: 0xffffff }))));
    const peakGlow = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color: 0xb18cff, transparent: true, opacity: 0.95, blending: THREE.AdditiveBlending, depthWrite: false })));
    peakGlow.scale.setScalar(1.2);
    peak.add(peakGlow);
    const ringGeo = track(new THREE.RingGeometry(0.2, 0.225, 64));
    const peakRings = [0, 0.5].map((offset) => {
        const m = new THREE.Mesh(ringGeo, track(new THREE.MeshBasicMaterial({ color: 0xc4a8ff, transparent: true, opacity: 0, side: THREE.DoubleSide, blending: THREE.AdditiveBlending, depthWrite: false })));
        m.userData.offset = offset;
        peak.add(m);
        return m;
    });

    // ---- Pins (HTML), anchored to points on the curve
    const avgU = nearestU(curve, toScene([360, 140]).x);
    const pins = [
        { text: container.dataset.pinAverage, u: avgU, peak: false },
        { text: container.dataset.pinPeak, u: 1, peak: true },
    ].filter((p) => p.text).map((p) => {
        const el = document.createElement('span');
        el.className = 'scene-label scene-label--pin' + (p.peak ? ' is-peak' : '');
        el.textContent = p.text;
        labelsEl.appendChild(el);
        return { ...p, el, point: curve.getPointAt(p.u) };
    });
    const avgDot = new THREE.Mesh(track(new THREE.SphereGeometry(0.075, 12, 12)), track(new THREE.MeshBasicMaterial({ color: 0xffffff })));
    avgDot.position.copy(curve.getPointAt(avgU));
    chart.add(avgDot);

    // ---- Layout
    let width = 1;
    let height = 1;
    function layout() {
        width = container.clientWidth || 1;
        height = container.clientHeight || 1;
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        const halfV = Math.tan(THREE.MathUtils.degToRad(camera.fov / 2));
        const needH = 2.75 / halfV;
        const needW = 4.15 / (halfV * camera.aspect);
        const dist = Math.max(needH, needW);
        camera.position.set(dist * 0.1, dist * 0.42, dist);
        camera.lookAt(0, 0.35, -0.4);
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

    const v = new THREE.Vector3();
    function placeLabel(el, pos, alpha) {
        v.copy(pos).project(camera);
        const x = (v.x * 0.5 + 0.5) * width;
        const y = (-v.y * 0.5 + 0.5) * height;
        el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0) translate(-50%, calc(-100% - 14px))`;
        el.style.opacity = alpha.toFixed(2);
    }

    // ---- Frame
    const heroH = container.closest('.page-hero')?.offsetHeight || 700;
    const clock = new THREE.Clock(false);
    const faceCam = new THREE.Quaternion();
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
        const draw = easeInOut(THREE.MathUtils.clamp(intro * 1.25, 0, 1)); // line draws in
        const after = THREE.MathUtils.clamp(intro * 2.5 - 1.4, 0, 1); // things that follow the line

        world.rotation.y = -0.52 + pointer.x * 0.28 + Math.sin(t * 0.18) * 0.06 + scroll * 0.35;
        world.rotation.x = pointer.y * 0.06;
        world.position.y = -scroll * 0.7;
        world.updateMatrixWorld();
        // Billboards live inside the rotated world: undo its rotation, then face the camera.
        const colYaw = Math.atan2(camera.position.x, camera.position.z) - world.rotation.y;
        faceCam.copy(world.quaternion).invert().multiply(camera.quaternion);

        tube.geometry.setDrawRange(0, Math.floor((tubeCount * draw) / 3) * 3);
        tubeGlow.geometry.setDrawRange(0, Math.floor((glowCount * draw) / 3) * 3);
        front.material.opacity = 0.32 * after;
        back.material.opacity = 0.22 * after;
        backLine.material.opacity = 0.6 * after;

        columns.forEach((c) => {
            const k = easeOutCubic(THREE.MathUtils.clamp((draw - c.u) * 6, 0, 1));
            c.col.scale.y = Math.max(0.001, c.h * k);
            c.col.position.y = FLOOR_Y + (c.h * k) / 2;
            c.col.material.opacity = (0.4 + Math.sin(t * 2 + c.u * 9) * 0.15) * k;
            c.dot.scale.setScalar(Math.max(0.001, k));
            c.col.rotation.y = colYaw; // stay upright, turned toward the camera
        });

        if (draw >= 1) {
            curve.getPointAt((t * 0.16) % 1, pulse.position);
            pulse.material.opacity = 0.95;
        }

        peak.scale.setScalar(Math.max(0.001, after));
        peakGlow.scale.setScalar(0.9 * (1 + Math.sin(t * 2.6) * 0.1));
        peakRings.forEach((r) => {
            const a = (t * 0.6 + r.userData.offset) % 1;
            r.scale.setScalar(1 + a * 1.7);
            r.material.opacity = (1 - a) * 0.7 * after;
            r.quaternion.copy(faceCam);
        });
        avgDot.scale.setScalar(Math.max(0.001, after));

        waveLand(t);
        landMat.opacity = 0.7 * Math.min(1, intro * 2);
        updateRise(dt);
        riseMat.opacity = 0.8 * after;

        pins.forEach((p) => {
            v.copy(p.point).applyMatrix4(world.matrixWorld);
            placeLabel(p.el, v, after);
        });

        renderer.render(scene, camera);
    }

    function loop() {
        if (!running) return;
        const dt = Math.min(clock.getDelta(), 1 / 30);
        t += dt;
        intro = Math.min(1, intro + dt / 2.2);
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
        t = 3;
        intro = 1;
        updateRise(0);
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

/** Curve parameter whose point is closest to x. */
function nearestU(curve, x) {
    let best = 0;
    let bestD = Infinity;
    const p = new THREE.Vector3();
    for (let i = 0; i <= 200; i++) {
        curve.getPointAt(i / 200, p);
        const d = Math.abs(p.x - x);
        if (d < bestD) { bestD = d; best = i / 200; }
    }
    return best;
}

function easeOutCubic(x) {
    return 1 - Math.pow(1 - x, 3);
}

function easeInOut(x) {
    return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2;
}

/** Vertical gradient: opaque at the top (v = 1), clear at the bottom. */
function makeFadeTexture() {
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
