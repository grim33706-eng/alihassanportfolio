/**
 * "Let's talk" band: a flowing field of light points behind the panel. The
 * waves are computed on the GPU; the pointer sends a ripple through the
 * surface wherever it moves over the panel.
 */
import { lowPower, makeRenderer, runLoop, glowTexture } from './kit.js';

export default function ctaScene(THREE, container) {
    const renderer = makeRenderer(THREE, container);
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(42, 1, 0.1, 60);
    camera.position.set(0, 2.1, 6.2);
    camera.lookAt(0, -0.2, -2.5);

    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };

    const COLS = lowPower ? 90 : 150;
    const ROWS = lowPower ? 34 : 54;
    const W = 22;
    const D = 13;
    const positions = new Float32Array(COLS * ROWS * 3);
    const rand = new Float32Array(COLS * ROWS);
    for (let r = 0, i = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++, i++) {
            positions[i * 3] = -W / 2 + (c / (COLS - 1)) * W;
            positions[i * 3 + 1] = 0;
            positions[i * 3 + 2] = 4 - (r / (ROWS - 1)) * D;
            rand[i] = Math.random();
        }
    }
    const geo = track(new THREE.BufferGeometry());
    geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geo.setAttribute('aRand', new THREE.BufferAttribute(rand, 1));

    const uniforms = {
        uTime: { value: 0 },
        uMouse: { value: new THREE.Vector2(0, -2) },
        uMouseAmp: { value: 0 },
        uScale: { value: 300 },
        uTex: { value: track(glowTexture(THREE)) },
    };
    const mat = track(new THREE.ShaderMaterial({
        uniforms,
        vertexShader: `
            uniform float uTime;
            uniform vec2 uMouse;
            uniform float uMouseAmp;
            uniform float uScale;
            attribute float aRand;
            varying float vH;
            varying float vX;
            varying float vFade;
            void main() {
                vec3 p = position;
                float w = sin(p.x * 0.45 + uTime * 0.6) * 0.32
                        + sin(p.z * 0.8 + uTime * 0.85) * 0.22
                        + sin((p.x + p.z) * 0.3 - uTime * 0.45) * 0.28;
                float d = distance(p.xz, uMouse);
                w += uMouseAmp * exp(-d * d * 0.22) * sin(d * 2.4 - uTime * 3.2) * 0.75;
                p.y += w;
                vH = w;
                vX = clamp((p.x + 11.0) / 22.0, 0.0, 1.0);
                vec4 mv = modelViewMatrix * vec4(p, 1.0);
                vFade = smoothstep(-16.0, -6.0, mv.z) * (0.55 + aRand * 0.45);
                gl_PointSize = uScale * (0.06 + aRand * 0.04) / -mv.z;
                gl_Position = projectionMatrix * mv;
            }`,
        fragmentShader: `
            uniform sampler2D uTex;
            varying float vH;
            varying float vX;
            varying float vFade;
            void main() {
                float a = texture2D(uTex, gl_PointCoord).a;
                vec3 blue = vec3(0.36, 0.55, 1.0);
                vec3 violet = vec3(0.62, 0.42, 1.0);
                vec3 teal = vec3(0.35, 0.94, 0.85);
                vec3 col = mix(blue, violet, vX);
                col = mix(col, teal, smoothstep(0.35, 0.8, vH) * 0.6);
                float glow = 0.7 + smoothstep(-0.4, 0.7, vH) * 0.8;
                gl_FragColor = vec4(col * glow, a * vFade * 0.9);
            }`,
        transparent: true,
        depthWrite: false,
        blending: THREE.AdditiveBlending,
    }));
    const field = new THREE.Points(geo, mat);
    field.frustumCulled = false;
    scene.add(field);

    // Pointer → point on the wave plane (y = 0), eased.
    const raycaster = new THREE.Raycaster();
    const plane = new THREE.Plane(new THREE.Vector3(0, 1, 0), 0);
    const ndc = new THREE.Vector2();
    const hit = new THREE.Vector3();
    const target = new THREE.Vector2(0, -2);
    let hover = 0;
    let hoverTarget = 0;
    const panel = container.closest('.cta-band__panel') || container;
    const onMove = (e) => {
        const r = container.getBoundingClientRect();
        ndc.set(((e.clientX - r.left) / r.width) * 2 - 1, -((e.clientY - r.top) / r.height) * 2 + 1);
        raycaster.setFromCamera(ndc, camera);
        if (raycaster.ray.intersectPlane(plane, hit)) target.set(hit.x, hit.z);
        hoverTarget = 1;
    };
    const onLeave = () => { hoverTarget = 0; };
    panel.addEventListener('pointermove', onMove, { passive: true });
    panel.addEventListener('pointerleave', onLeave);

    const resize = () => {
        const w = container.clientWidth || 1;
        const h = container.clientHeight || 1;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        uniforms.uScale.value = (h * renderer.getPixelRatio()) / (2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)));
    };

    const { dispose } = runLoop(THREE, {
        container, renderer, scene, camera, resize, disposables,
        frame(t) {
            uniforms.uTime.value = t;
            uniforms.uMouse.value.lerp(target, 0.08);
            hover += (hoverTarget - hover) * 0.05;
            uniforms.uMouseAmp.value = hover;
            field.rotation.y = Math.sin(t * 0.08) * 0.06;
        },
    });
    window.App?.onTeardown(() => {
        panel.removeEventListener('pointermove', onMove);
        panel.removeEventListener('pointerleave', onLeave);
    });
    return dispose;
}
