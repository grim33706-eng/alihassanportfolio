/**
 * International page: a dotted globe with markers for each market and arcs
 * drawn from Lahore (home base). Drag to rotate; auto-rotates slowly.
 * Labels are HTML elements projected from 3D positions.
 */
import * as THREE from 'three';

const container = document.querySelector('[data-globe]');

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

if (container && webglAvailable()) whenIdle(init, { timeout: 800 });

function latLngToVec(lat, lng, r) {
    const phi = THREE.MathUtils.degToRad(90 - lat);
    const theta = THREE.MathUtils.degToRad(lng + 180);
    return new THREE.Vector3(
        -r * Math.sin(phi) * Math.cos(theta),
        r * Math.cos(phi),
        r * Math.sin(phi) * Math.sin(theta),
    );
}

function init() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const markets = JSON.parse(container.dataset.markets || '[]');
    const labelsEl = container.querySelector('[data-globe-labels]');
    const R = 2;

    const renderer = new THREE.WebGLRenderer({ antialias: !lowPower, alpha: true, powerPreference: 'high-performance' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, lowPower ? 1 : 1.5));
    container.prepend(renderer.domElement);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(35, 1, 0.1, 50);
    camera.position.set(0, 0, 7.6);

    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };

    const globe = new THREE.Group();
    scene.add(globe);

    // Occluding core so back-facing dots fade behind the sphere.
    const core = new THREE.Mesh(
        track(new THREE.SphereGeometry(R * 0.985, 64, 64)),
        track(new THREE.MeshBasicMaterial({ color: 0x0d1428, transparent: true, opacity: 0.92 })),
    );
    globe.add(core);

    // Soft fresnel rim: bright only at the silhouette.
    const rim = new THREE.Mesh(
        track(new THREE.SphereGeometry(R * 1.015, 64, 64)),
        track(new THREE.ShaderMaterial({
            transparent: true,
            depthWrite: false,
            blending: THREE.AdditiveBlending,
            uniforms: { uColor: { value: new THREE.Color(0x4f7cff) } },
            vertexShader: 'varying vec3 vN; void main(){ vN = normalize(normalMatrix * normal); gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
            fragmentShader: 'uniform vec3 uColor; varying vec3 vN; void main(){ float i = pow(1.0 - abs(vN.z), 4.0); gl_FragColor = vec4(uColor, i * 0.55); }',
        })),
    );
    scene.add(rim);

    // Fibonacci dot sphere.
    const dots = window.innerWidth < 768 ? 1400 : 2600;
    const pos = new Float32Array(dots * 3);
    const golden = Math.PI * (3 - Math.sqrt(5));
    for (let i = 0; i < dots; i++) {
        const y = 1 - (i / (dots - 1)) * 2;
        const rad = Math.sqrt(1 - y * y);
        const th = golden * i;
        pos[i * 3] = Math.cos(th) * rad * R;
        pos[i * 3 + 1] = y * R;
        pos[i * 3 + 2] = Math.sin(th) * rad * R;
    }
    const dotGeo = track(new THREE.BufferGeometry());
    dotGeo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    const dotMat = track(new THREE.PointsMaterial({ color: 0x5f7fd9, size: 0.022, transparent: true, opacity: 0.75 }));
    globe.add(new THREE.Points(dotGeo, dotMat));

    // Latitude rings for structure.
    const ringMat = track(new THREE.LineBasicMaterial({ color: 0x293247, transparent: true, opacity: 0.6 }));
    [-60, -30, 0, 30, 60].forEach((lat) => {
        const pts = [];
        for (let lng = -180; lng <= 180; lng += 4) pts.push(latLngToVec(lat, lng, R * 1.001));
        globe.add(new THREE.Line(track(new THREE.BufferGeometry().setFromPoints(pts)), ringMat));
    });

    // Markers, pulses and arcs.
    const home = markets.find((m) => m.code === 'PK') || markets[0];
    const homeVec = latLngToVec(home.lat, home.lng, R);
    const markerGeo = track(new THREE.SphereGeometry(0.045, 16, 16));
    const pulseGeo = track(new THREE.RingGeometry(0.06, 0.08, 32));
    const markerMat = track(new THREE.MeshBasicMaterial({ color: 0x9db3ff }));
    const homeMat = track(new THREE.MeshBasicMaterial({ color: 0x2dd4bf }));

    const markers = [];
    const arcs = [];

    markets.forEach((m, i) => {
        const isHome = m === home;
        const v = latLngToVec(m.lat, m.lng, R);

        const dot = new THREE.Mesh(markerGeo, isHome ? homeMat : markerMat);
        dot.position.copy(v);
        globe.add(dot);

        const pulseMat = track(new THREE.MeshBasicMaterial({ color: isHome ? 0x2dd4bf : 0x6d93ff, transparent: true, opacity: 0.8, side: THREE.DoubleSide, depthWrite: false }));
        const pulse = new THREE.Mesh(pulseGeo, pulseMat);
        pulse.position.copy(v.clone().multiplyScalar(1.002));
        pulse.lookAt(v.clone().multiplyScalar(2));
        pulse.userData.offset = i * 0.37;
        globe.add(pulse);

        const label = document.createElement('span');
        label.className = 'globe-label' + (isHome ? ' globe-label--home' : '');
        label.textContent = isHome ? `${m.code} · Home base` : m.code;
        labelsEl.appendChild(label);
        markers.push({ v, label, pulse, code: m.code });

        if (!isHome) {
            const mid = homeVec.clone().add(v).multiplyScalar(0.5);
            const dist = homeVec.distanceTo(v);
            mid.setLength(R + dist * 0.35);
            const curve = new THREE.QuadraticBezierCurve3(homeVec, mid, v);
            const geo = track(new THREE.BufferGeometry().setFromPoints(curve.getPoints(80)));
            const mat = track(new THREE.LineBasicMaterial({ color: 0x8b5cf6, transparent: true, opacity: 0.85 }));
            const line = new THREE.Line(geo, mat);
            geo.setDrawRange(0, reduced ? 81 : 0);
            globe.add(line);
            arcs.push({ geo, delay: 0.4 + arcs.length * 0.18 });
        }
    });

    // Start facing the Europe–Asia markets, tilted to show the north.
    const centre = latLngToVec(35, 45, R);
    globe.rotation.y = -Math.atan2(centre.x, centre.z);
    globe.rotation.x = 0.35;

    // ---- Size
    let size = 1; // cached for the label projection in the render loop
    function layout() {
        const s = container.clientWidth;
        size = s || 1;
        renderer.setSize(s, s, false);
        camera.aspect = 1;
        camera.updateProjectionMatrix();
    }
    layout();

    // ---- Drag to rotate
    let dragging = false;
    let lastX = 0;
    let lastY = 0;
    let velocity = 0;
    const canvas = renderer.domElement;
    canvas.style.touchAction = 'pan-y';
    canvas.style.cursor = 'grab';

    const onDown = (e) => { dragging = true; lastX = e.clientX; lastY = e.clientY; canvas.style.cursor = 'grabbing'; };
    const onMove = (e) => {
        if (!dragging) return;
        const dx = e.clientX - lastX;
        const dy = e.clientY - lastY;
        lastX = e.clientX;
        lastY = e.clientY;
        globe.rotation.y += dx * 0.006;
        globe.rotation.x = THREE.MathUtils.clamp(globe.rotation.x + dy * 0.004, -0.6, 0.9);
        velocity = dx * 0.006;
        if (reduced) renderOnce();
    };
    const onUp = () => { dragging = false; canvas.style.cursor = 'grab'; };
    canvas.addEventListener('pointerdown', onDown);
    window.addEventListener('pointermove', onMove);
    window.addEventListener('pointerup', onUp);

    // ---- Labels
    // Neighbouring markets (UK/France, UAE/Oman/Saudi) get labels on different
    // sides of their marker so they don't overlap.
    const labelSide = { UK: 'left', SA: 'left', AE: 'top', FR: 'below', OM: 'below', BD: 'below' };
    function labelTransform(code, x, y) {
        switch (labelSide[code]) {
            case 'left': return `translate(${x - 12}px, ${y}px) translate(-100%, -50%)`;
            case 'top': return `translate(${x}px, ${y - 12}px) translate(-50%, -100%)`;
            case 'below': return `translate(${x + 8}px, ${y + 8}px)`;
            default: return `translate(${x + 10}px, ${y - 10}px)`;
        }
    }
    const tmp = new THREE.Vector3();
    const camDir = new THREE.Vector3();
    function updateLabels() {
        const w = size;
        const h = size;
        camera.getWorldDirection(camDir);
        markers.forEach(({ v, label, code }) => {
            tmp.copy(v).applyMatrix4(globe.matrixWorld);
            const facing = tmp.clone().normalize().dot(camDir) < -0.15;
            tmp.project(camera);
            const x = (tmp.x * 0.5 + 0.5) * w;
            const y = (-tmp.y * 0.5 + 0.5) * h;
            label.style.transform = labelTransform(code, x, y);
            label.style.opacity = facing ? '1' : '0';
        });
    }

    // ---- Loop
    const clock = new THREE.Clock(false);
    let elapsed = 0; // carries on across pauses so arcs don't redraw from scratch
    let running = false;
    let frame = 0;
    let inView = true;

    function renderOnce() {
        globe.updateMatrixWorld();
        updateLabels();
        renderer.render(scene, camera);
    }

    function loop() {
        if (!running) return;
        elapsed += Math.min(clock.getDelta(), 1 / 30);
        const t = elapsed;

        if (!dragging) {
            velocity *= 0.95;
            globe.rotation.y += 0.0016 + velocity;
        }

        arcs.forEach(({ geo, delay }) => {
            const p = THREE.MathUtils.clamp((t - delay) / 1.2, 0, 1);
            geo.setDrawRange(0, Math.floor(p * 81));
        });
        markers.forEach(({ pulse }) => {
            const k = ((t * 0.6 + pulse.userData.offset) % 1);
            pulse.scale.setScalar(1 + k * 2.2);
            pulse.material.opacity = 0.8 * (1 - k);
        });

        renderOnce();
        frame = requestAnimationFrame(loop);
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

    const io = new IntersectionObserver(([entry]) => {
        inView = entry.isIntersecting;
        inView && !document.hidden ? start() : stop();
    });
    io.observe(container);
    const onVisibility = () => (document.hidden || !inView ? stop() : start());
    document.addEventListener('visibilitychange', onVisibility);

    const onResize = () => { layout(); renderOnce(); };
    window.addEventListener('resize', onResize);

    container.classList.add('webgl-ready');
    reduced ? renderOnce() : start();

    window.App?.onTeardown(() => {
        stop();
        io.disconnect();
        window.removeEventListener('pointermove', onMove);
        window.removeEventListener('pointerup', onUp);
        window.removeEventListener('resize', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        disposables.forEach((d) => d.dispose && d.dispose());
        renderer.dispose();
        renderer.forceContextLoss();
    });
}
