/**
 * About hero 3D scene: an "expertise system". A holographic core carrying the
 * monogram sits at the centre; each of the real skill groups (data-groups)
 * travels on its own tilted orbit as a glowing node with a comet trail and a
 * floating label. A soft particle sphere and links to the core frame it.
 *
 * Renders only while the hero is on screen and the tab is visible, at a
 * lighter quality on low-power devices. With reduced motion it draws one
 * still frame. Without WebGL the CSS orbits in the markup stay.
 */
import * as THREE from 'three';
import { lowPower, webglAvailable, makeRenderer, runLoop, glowTexture } from './kit.js';

const container = document.querySelector('[data-about-scene]');
const whenIdle = window.requestIdleCallback || ((fn) => setTimeout(fn, 200));
// The scene sits beside the headline; below this width it is hidden by CSS, so skip it entirely.
const wideEnough = window.matchMedia('(min-width: 992px)').matches;

if (container && wideEnough && webglAvailable()) {
    // The monogram is drawn to a canvas, so wait for the web fonts first.
    whenIdle(() => (document.fonts?.ready || Promise.resolve()).then(init), { timeout: 800 });
}

const COLORS = ['#5b8cff', '#2dd4bf', '#8b5cf6', '#ff5c8a', '#f5b85b'];

function init() {
    let groups = [];
    try { groups = JSON.parse(container.dataset.groups || '[]'); } catch (e) { groups = []; }
    groups = groups.slice(0, 6);
    const monogram = container.dataset.monogram || '';
    const labelsEl = container.querySelector('[data-scene-labels]');

    const renderer = makeRenderer(THREE, container);
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 60);
    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };
    const glowTex = track(glowTexture(THREE));

    const world = new THREE.Group();
    scene.add(world);

    // ---- Core: fresnel sphere, wire shell, inner glow and the monogram
    const core = new THREE.Group();
    world.add(core);
    const coreMat = track(new THREE.ShaderMaterial({
        uniforms: { uTime: { value: 0 } },
        vertexShader: `
            varying vec3 vNormal;
            varying vec3 vView;
            void main() {
                vec4 mv = modelViewMatrix * vec4(position, 1.0);
                vNormal = normalize(normalMatrix * normal);
                vView = normalize(-mv.xyz);
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: `
            uniform float uTime;
            varying vec3 vNormal;
            varying vec3 vView;
            void main() {
                float f = pow(1.0 - max(dot(vNormal, vView), 0.0), 2.4);
                vec3 inner = vec3(0.10, 0.13, 0.32);
                vec3 rim = mix(vec3(0.42, 0.58, 1.0), vec3(0.70, 0.50, 1.0), 0.5 + 0.5 * sin(uTime * 0.8));
                gl_FragColor = vec4(mix(inner, rim, f), 0.55 + f * 0.45);
            }`,
        transparent: true,
        depthWrite: false,
    }));
    const coreBall = new THREE.Mesh(track(new THREE.SphereGeometry(0.78, 48, 48)), coreMat);
    core.add(coreBall);
    const shell = new THREE.Mesh(
        track(new THREE.IcosahedronGeometry(1.02, 1)),
        track(new THREE.MeshBasicMaterial({ color: 0x6d93ff, wireframe: true, transparent: true, opacity: 0.22, blending: THREE.AdditiveBlending, depthWrite: false })),
    );
    core.add(shell);
    const coreGlow = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color: 0x6d93ff, transparent: true, opacity: 0.55, blending: THREE.AdditiveBlending, depthWrite: false })));
    coreGlow.scale.setScalar(3.4);
    core.add(coreGlow);
    if (monogram) {
        const mono = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: track(monogramTexture(monogram)), transparent: true, depthWrite: false, depthTest: false })));
        mono.scale.set(1.0, 0.5, 1);
        mono.renderOrder = 5;
        core.add(mono);
    }

    // ---- Orbits, one per skill group
    const orbits = groups.map((name, i) => {
        const color = new THREE.Color(COLORS[i % COLORS.length]);
        const radius = 1.75 + i * 0.32;
        const pivot = new THREE.Group();
        // Spread the orbit planes like an atom's.
        pivot.rotation.set(1.15 + (i % 2 ? 0.35 : -0.25) + i * 0.08, (i / groups.length) * Math.PI, (i - groups.length / 2) * 0.18);
        world.add(pivot);

        const ring = new THREE.Mesh(
            track(new THREE.TorusGeometry(radius, 0.006, 6, 220)),
            track(new THREE.MeshBasicMaterial({ color, transparent: true, opacity: 0.32, blending: THREE.AdditiveBlending, depthWrite: false })),
        );
        pivot.add(ring);

        const node = new THREE.Group();
        pivot.add(node);
        node.add(new THREE.Mesh(track(new THREE.SphereGeometry(0.075, 16, 16)), track(new THREE.MeshBasicMaterial({ color: color.clone().lerp(new THREE.Color(0xffffff), 0.45) }))));
        const halo = new THREE.Sprite(track(new THREE.SpriteMaterial({ map: glowTex, color, transparent: true, opacity: 0.95, blending: THREE.AdditiveBlending, depthWrite: false })));
        halo.scale.setScalar(0.75);
        node.add(halo);

        // Comet trail: a line whose colour fades to black (invisible with additive blending).
        const TRAIL = 46;
        const trailGeo = track(new THREE.BufferGeometry());
        trailGeo.setAttribute('position', new THREE.BufferAttribute(new Float32Array(TRAIL * 3), 3).setUsage(THREE.DynamicDrawUsage));
        const tc = new Float32Array(TRAIL * 3);
        for (let k = 0; k < TRAIL; k++) {
            const f = Math.pow(1 - k / (TRAIL - 1), 1.6);
            tc[k * 3] = color.r * f;
            tc[k * 3 + 1] = color.g * f;
            tc[k * 3 + 2] = color.b * f;
        }
        trailGeo.setAttribute('color', new THREE.BufferAttribute(tc, 3));
        const trail = new THREE.Line(trailGeo, track(new THREE.LineBasicMaterial({ vertexColors: true, transparent: true, blending: THREE.AdditiveBlending, depthWrite: false })));
        trail.frustumCulled = false;
        pivot.add(trail);

        const label = document.createElement('span');
        label.className = 'scene-label scene-label--platform';
        label.style.setProperty('--dot', COLORS[i % COLORS.length]);
        label.textContent = name;
        labelsEl.appendChild(label);

        return { pivot, ring, node, halo, trail, trailGeo, TRAIL, radius, label,
            speed: 0.16 + (i % 3) * 0.05 * (i % 2 ? -1 : 1) + 0.02 * i,
            phase: (i / groups.length) * Math.PI * 2 };
    });

    // Links from the core to each node.
    const linkGeo = track(new THREE.BufferGeometry());
    linkGeo.setAttribute('position', new THREE.BufferAttribute(new Float32Array(orbits.length * 2 * 3), 3).setUsage(THREE.DynamicDrawUsage));
    const linkMat = track(new THREE.LineBasicMaterial({ color: 0x6d93ff, transparent: true, opacity: 0.16, blending: THREE.AdditiveBlending, depthWrite: false }));
    const links = new THREE.LineSegments(linkGeo, linkMat);
    links.frustumCulled = false;
    world.add(links);

    // ---- Particle sphere around everything (Fibonacci lattice)
    const SPHERE = lowPower ? 420 : 900;
    const sp = new Float32Array(SPHERE * 3);
    const golden = Math.PI * (3 - Math.sqrt(5));
    for (let i = 0; i < SPHERE; i++) {
        const y = 1 - (i / (SPHERE - 1)) * 2;
        const r = Math.sqrt(1 - y * y);
        const a = golden * i;
        const R = 3.55 + (Math.random() - 0.5) * 0.25;
        sp[i * 3] = Math.cos(a) * r * R;
        sp[i * 3 + 1] = y * R;
        sp[i * 3 + 2] = Math.sin(a) * r * R;
    }
    const sphereGeo = track(new THREE.BufferGeometry());
    sphereGeo.setAttribute('position', new THREE.BufferAttribute(sp, 3));
    const sphereMat = track(new THREE.PointsMaterial({ size: 0.065, map: glowTex, color: 0x8fa6ff, transparent: true, opacity: 0.7, depthWrite: false, blending: THREE.AdditiveBlending }));
    const sphere = new THREE.Points(sphereGeo, sphereMat);
    world.add(sphere);

    // ---- Layout
    let width = 1;
    let height = 1;
    const resize = () => {
        width = container.clientWidth || 1;
        height = container.clientHeight || 1;
        renderer.setSize(width, height, false);
        camera.aspect = width / height;
        const halfV = Math.tan(THREE.MathUtils.degToRad(camera.fov / 2));
        const dist = Math.max(3.75 / halfV, 3.9 / (halfV * camera.aspect));
        camera.position.set(0, dist * 0.08, dist);
        camera.lookAt(0, 0, 0);
        camera.updateProjectionMatrix();
    };

    // ---- Pointer parallax
    const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
    const onMove = (e) => {
        pointer.tx = e.clientX / window.innerWidth - 0.5;
        pointer.ty = e.clientY / window.innerHeight - 0.5;
    };
    window.addEventListener('pointermove', onMove, { passive: true });

    const v = new THREE.Vector3();
    const p = new THREE.Vector3();
    const placed = [];
    const heroH = container.closest('.page-hero')?.offsetHeight || 700;
    let intro = 0;

    runLoop(THREE, {
        container, renderer, scene, camera, resize, disposables,
        frame(t, dt) {
            intro = dt === 0 ? 1 : Math.min(1, intro + dt / 2);
            const e = easeOutBack(Math.min(1, intro * 1.4));
            pointer.x += (pointer.tx - pointer.x) * 0.05;
            pointer.y += (pointer.ty - pointer.y) * 0.05;
            const scroll = Math.min(window.scrollY / heroH, 1);

            world.rotation.y = t * 0.06 + pointer.x * 0.4 + scroll * 0.8;
            world.rotation.x = 0.12 + pointer.y * 0.18;
            world.position.y = -scroll * 0.8;
            world.updateMatrixWorld();

            coreMat.uniforms.uTime.value = t;
            const beat = 1 + Math.sin(t * 1.8) * 0.03;
            coreBall.scale.setScalar(Math.max(0.001, e * beat));
            shell.scale.setScalar(Math.max(0.001, e));
            shell.rotation.set(t * 0.18, t * 0.24, 0);
            coreGlow.material.opacity = 0.5 * Math.min(1, intro * 2) * (0.85 + Math.sin(t * 1.8) * 0.15);

            const linkPos = linkGeo.attributes.position;
            orbits.forEach((o, i) => {
                const k = easeOutCubic(THREE.MathUtils.clamp(intro * 2 - 0.2 - i * 0.12, 0, 1));
                o.ring.scale.setScalar(Math.max(0.001, 0.6 + k * 0.4));
                o.ring.material.opacity = 0.32 * k;
                const a = o.phase + t * o.speed;
                o.node.position.set(Math.cos(a) * o.radius, Math.sin(a) * o.radius, 0);
                o.node.scale.setScalar(Math.max(0.001, k));
                o.halo.material.opacity = 0.95 * k * (0.8 + Math.sin(t * 3 + i) * 0.2);

                const tp = o.trailGeo.attributes.position;
                for (let s = 0; s < o.TRAIL; s++) {
                    const b = a - Math.sign(o.speed) * s * 0.03;
                    tp.setXYZ(s, Math.cos(b) * o.radius, Math.sin(b) * o.radius, 0);
                }
                tp.needsUpdate = true;
                o.trail.material.opacity = k;

                // World position of the node → link to the core and the HTML label.
                o.node.getWorldPosition(p);
                linkPos.setXYZ(i * 2, 0, world.position.y, 0);
                linkPos.setXYZ(i * 2 + 1, p.x, p.y, p.z);

                v.copy(p).project(camera);
                placed[i] = {
                    o,
                    x: (v.x * 0.5 + 0.5) * width,
                    y: (-v.y * 0.5 + 0.5) * height - 22,
                    depth: THREE.MathUtils.clamp((p.z + o.radius) / (2 * o.radius), 0, 1), // 1 = nearest
                    alpha: k,
                };
            });

            // Labels: hide those behind the core, and where two collide keep the nearer one.
            v.set(0, world.position.y, 0).project(camera);
            const cx = (v.x * 0.5 + 0.5) * width;
            const cy = (-v.y * 0.5 + 0.5) * height;
            const coreR = height * 0.16;
            placed.forEach((a) => {
                a.w = a.o.labelW || (a.o.labelW = a.o.label.offsetWidth || 140);
                if (a.depth < 0.5 && Math.hypot(a.x - cx, a.y + 22 - cy) < coreR) a.alpha = 0;
            });
            const byDepth = [...placed].sort((a, b) => b.depth - a.depth);
            byDepth.forEach((a, i) => {
                for (let j = 0; j < i; j++) {
                    const b = byDepth[j];
                    if (b.alpha > 0.05 && Math.abs(a.x - b.x) < (a.w + b.w) / 2 + 6 && Math.abs(a.y - b.y) < 30) {
                        a.alpha = 0;
                        break;
                    }
                }
            });
            placed.forEach((a) => {
                const target = a.alpha * (0.35 + a.depth * 0.65);
                a.o.shown = (a.o.shown || 0) + (target - (a.o.shown || 0)) * (dt === 0 ? 1 : 0.18);
                a.o.label.style.transform = `translate3d(${a.x.toFixed(1)}px, ${a.y.toFixed(1)}px, 0) translate(-50%, -50%)`;
                a.o.label.style.opacity = a.o.shown.toFixed(2);
            });
            linkPos.needsUpdate = true;
            linkMat.opacity = 0.16 * Math.min(1, intro * 1.5);

            sphere.rotation.y = -t * 0.03;
            sphere.rotation.x = Math.sin(t * 0.1) * 0.1;
            sphereMat.opacity = 0.7 * Math.min(1, intro * 1.5);
        },
    });
    window.App?.onTeardown(() => window.removeEventListener('pointermove', onMove));
}

function monogramTexture(text) {
    const c = document.createElement('canvas');
    c.width = 512;
    c.height = 256;
    const ctx = c.getContext('2d');
    ctx.font = '700 150px "Sora", "Inter", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    const g = ctx.createLinearGradient(130, 0, 382, 0);
    g.addColorStop(0, '#9db3ff');
    g.addColorStop(0.55, '#ffffff');
    g.addColorStop(1, '#c4a8ff');
    ctx.shadowColor = 'rgba(109, 147, 255, 0.9)';
    ctx.shadowBlur = 28;
    ctx.fillStyle = g;
    ctx.fillText(text, 256, 140);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

function easeOutCubic(x) {
    return 1 - Math.pow(1 - x, 3);
}

function easeOutBack(x) {
    const c1 = 1.5;
    const c3 = c1 + 1;
    return 1 + c3 * Math.pow(x - 1, 3) + c1 * Math.pow(x - 1, 2);
}
