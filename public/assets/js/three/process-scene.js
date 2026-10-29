/**
 * Services → Process: a 3D pipeline above the step cards. One node sits over
 * each step's column; as the section scrolls through the viewport the pipe
 * lights up node by node, energy flows along the lit part, and the matching
 * step card is highlighted (.is-active).
 */
import { makeRenderer, runLoop, glowTexture, fadeTexture } from './kit.js';

export default function processScene(THREE, container) {
    const list = container.parentElement.querySelector('[data-process]');
    const steps = list ? [...list.querySelectorAll('.process__step')] : [];
    const N = steps.length;
    if (!N) return null;

    const renderer = makeRenderer(THREE, container);
    const scene = new THREE.Scene();
    // Orthographic, tilted about X only: node x positions line up with the columns exactly.
    const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 50);
    camera.position.set(0, 3.2, 6);
    camera.lookAt(0, 0, 0);

    const disposables = [];
    const track = (o) => { disposables.push(o); return o; };
    const glowTex = track(glowTexture(THREE));
    const fadeTex = track(fadeTexture(THREE));

    const world = new THREE.Group();
    world.position.y = -0.4;
    scene.add(world);

    // Rebuilt on resize, since node positions follow the grid columns.
    let built = null;
    const PX = 100; // pixels per world unit

    function build() {
        if (built) {
            world.remove(built.group);
            built.dispose.forEach((d) => d.dispose());
        }
        const own = [];
        const keep = (o) => { own.push(o); return o; };
        const group = new THREE.Group();
        world.add(group);

        const listRect = list.getBoundingClientRect();
        const rect = container.getBoundingClientRect();
        const xs = steps.map((s) => {
            const r = s.getBoundingClientRect();
            return (r.left + r.width / 2 - rect.left - rect.width / 2) / PX;
        });
        const span = listRect.width / PX;

        // Pipe path: weaves forward and back between the nodes, and runs off both edges.
        const pts = [new THREE.Vector3(-span / 2 - 0.8, 0, 0.9)];
        xs.forEach((x, i) => pts.push(new THREE.Vector3(x, 0, i % 2 ? -0.55 : 0.55)));
        pts.push(new THREE.Vector3(span / 2 + 0.8, 0, -0.9));
        const curve = new THREE.CatmullRomCurve3(pts, false, 'centripetal');
        const lengths = curve.getLengths(400);
        const total = lengths[lengths.length - 1];
        // Curve parameter (0–1, by arc length) of each node.
        const nodeU = xs.map((x, i) => {
            const target = pts[i + 1];
            let best = 0;
            let bestD = Infinity;
            for (let k = 0; k <= 400; k++) {
                const p = curve.getPointAt(k / 400);
                const d = p.distanceToSquared(target);
                if (d < bestD) { bestD = d; best = k / 400; }
            }
            return best;
        });

        const baseTube = new THREE.Mesh(
            keep(new THREE.TubeGeometry(curve, 300, 0.03, 8, false)),
            keep(new THREE.MeshBasicMaterial({ color: 0x293247, transparent: true, opacity: 0.9 })),
        );
        group.add(baseTube);
        const litGeo = keep(new THREE.TubeGeometry(curve, 300, 0.045, 8, false));
        {
            const pos = litGeo.attributes.position;
            const colors = [];
            const c = new THREE.Color();
            for (let i = 0; i < pos.count; i++) {
                const u = THREE.MathUtils.clamp((pos.getX(i) + span / 2) / span, 0, 1);
                c.set('#6d93ff').lerp(new THREE.Color('#b18cff'), u);
                colors.push(c.r, c.g, c.b);
            }
            litGeo.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));
        }
        const lit = new THREE.Mesh(litGeo, keep(new THREE.MeshBasicMaterial({ vertexColors: true })));
        group.add(lit);
        const litGlow = new THREE.Mesh(
            keep(new THREE.TubeGeometry(curve, 200, 0.16, 8, false)),
            keep(new THREE.MeshBasicMaterial({ color: 0x6d93ff, transparent: true, opacity: 0.18, blending: THREE.AdditiveBlending, depthWrite: false })),
        );
        group.add(litGlow);

        // Energy particles that flow along the lit stretch.
        const FLOW = 70;
        const flowPos = new Float32Array(FLOW * 3);
        const flowGeo = keep(new THREE.BufferGeometry());
        flowGeo.setAttribute('position', new THREE.BufferAttribute(flowPos, 3).setUsage(THREE.DynamicDrawUsage));
        const flowMat = keep(new THREE.PointsMaterial({ size: 0.16, map: glowTex, color: 0xdfe6ff, transparent: true, opacity: 0.9, blending: THREE.AdditiveBlending, depthWrite: false }));
        const flow = new THREE.Points(flowGeo, flowMat);
        flow.frustumCulled = false;
        group.add(flow);
        const flowSeeds = Array.from({ length: FLOW }, () => ({ o: Math.random(), s: 0.08 + Math.random() * 0.1, j: (Math.random() - 0.5) * 0.08 }));

        // Nodes
        const ringGeo = keep(new THREE.TorusGeometry(0.42, 0.025, 8, 80));
        const outerGeo = keep(new THREE.TorusGeometry(0.62, 0.008, 6, 80));
        const discGeo = keep(new THREE.CircleGeometry(0.4, 48));
        const coreGeo = keep(new THREE.OctahedronGeometry(0.2, 0));
        const beamGeo = keep(new THREE.CylinderGeometry(0.3, 0.34, 1.0, 32, 1, true));
        const nodes = xs.map((x, i) => {
            const p = pts[i + 1];
            const g = new THREE.Group();
            g.position.copy(p);
            group.add(g);
            const color = new THREE.Color('#6d93ff').lerp(new THREE.Color('#b18cff'), N > 1 ? i / (N - 1) : 0);
            const disc = new THREE.Mesh(discGeo, keep(new THREE.MeshBasicMaterial({ color: 0x0f1529, transparent: true, opacity: 0.95, side: THREE.DoubleSide })));
            disc.rotation.x = -Math.PI / 2;
            const ring = new THREE.Mesh(ringGeo, keep(new THREE.MeshBasicMaterial({ color: 0x3a4560 })));
            ring.rotation.x = Math.PI / 2;
            const outer = new THREE.Mesh(outerGeo, keep(new THREE.MeshBasicMaterial({ color, transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false })));
            outer.rotation.x = Math.PI / 2;
            const core = new THREE.Mesh(coreGeo, keep(new THREE.MeshBasicMaterial({ color, wireframe: true, transparent: true, opacity: 0.25 })));
            core.position.y = 0.55;
            const glow = new THREE.Sprite(keep(new THREE.SpriteMaterial({ map: glowTex, color, transparent: true, opacity: 0, blending: THREE.AdditiveBlending, depthWrite: false })));
            glow.scale.setScalar(1.6);
            const beam = new THREE.Mesh(beamGeo, keep(new THREE.MeshBasicMaterial({ map: fadeTex, color, transparent: true, opacity: 0, side: THREE.DoubleSide, blending: THREE.AdditiveBlending, depthWrite: false })));
            beam.position.y = 0.8;
            beam.rotation.x = Math.PI; // bright at the base
            g.add(disc, ring, outer, core, glow, beam);
            return { g, ring, outer, core, glow, beam, color, on: 0 };
        });

        built = { group, curve, nodes, nodeU, lit, litGlow, flow, flowPos, flowGeo, flowSeeds, total, dispose: own,
            litCount: litGeo.index.count, glowCount: litGlow.geometry.index.count };
    }

    const resize = () => {
        const w = container.clientWidth || 1;
        const h = container.clientHeight || 1;
        renderer.setSize(w, h, false);
        camera.left = -w / 2 / PX;
        camera.right = w / 2 / PX;
        camera.top = h / 2 / PX;
        camera.bottom = -h / 2 / PX;
        camera.updateProjectionMatrix();
        build();
    };

    // Section progress through the viewport → how far the pipe is lit.
    const section = container.closest('section') || container;
    const progress = () => {
        const r = section.getBoundingClientRect();
        const vh = window.innerHeight;
        return THREE.MathUtils.clamp((vh * 0.82 - r.top) / (r.height * 0.75), 0, 1);
    };
    let shown = 0;
    const ringLit = new THREE.Color();
    const flowPoint = new THREE.Vector3();

    const { dispose } = runLoop(THREE, {
        container, renderer, scene, camera, resize, disposables,
        frame(t, dt) {
            if (!built) return;
            const target = dt === 0 ? 1 : progress();
            shown += (target - shown) * Math.min(1, dt * 4 || 1);
            const { nodes, nodeU } = built;
            // Lit up to just past the last reached node, gliding in between.
            const reach = THREE.MathUtils.lerp(nodeU[0] * 0.6, 1, shown);

            built.lit.geometry.setDrawRange(0, Math.floor((built.litCount * reach) / 3) * 3);
            built.litGlow.geometry.setDrawRange(0, Math.floor((built.glowCount * reach) / 3) * 3);

            nodes.forEach((n, i) => {
                const active = reach >= nodeU[i] - 0.005;
                n.on += ((active ? 1 : 0) - n.on) * Math.min(1, dt * 5 || 1);
                const pulse = 1 + Math.sin(t * 2.4 + i) * 0.06;
                ringLit.set(0x3a4560).lerp(n.color, n.on);
                n.ring.material.color.copy(ringLit);
                n.outer.material.opacity = 0.7 * n.on;
                n.outer.scale.setScalar(pulse + n.on * 0.1);
                n.outer.rotation.z = t * 0.6;
                n.glow.material.opacity = 0.75 * n.on;
                n.beam.material.opacity = 0.35 * n.on;
                n.beam.scale.y = 0.2 + n.on * 0.8;
                n.beam.position.y = 0.5 * n.beam.scale.y;
                n.core.material.opacity = 0.25 + n.on * 0.65;
                n.core.position.y = 0.5 + n.on * 0.22 + Math.sin(t * 1.5 + i) * 0.06 * n.on;
                n.core.rotation.set(t * 0.7, t * 0.9, 0);
                n.core.scale.setScalar(0.8 + n.on * 0.5);
                if (steps[i]) steps[i].classList.toggle('is-active', active);
            });

            const p = flowPoint;
            built.flowSeeds.forEach((s, i) => {
                const u = ((s.o + t * s.s) % 1) * reach;
                built.curve.getPointAt(u, p);
                built.flowPos[i * 3] = p.x;
                built.flowPos[i * 3 + 1] = p.y + s.j;
                built.flowPos[i * 3 + 2] = p.z;
            });
            built.flowGeo.attributes.position.needsUpdate = true;
            world.rotation.x = Math.sin(t * 0.3) * 0.02;
        },
    });
    window.App?.onTeardown(() => built && built.dispose.forEach((d) => d.dispose()));
    return dispose;
}
