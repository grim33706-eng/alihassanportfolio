/**
 * In-page 3D scenes below the hero. Each scene's code (and Three.js itself)
 * is only fetched once its section comes near the viewport.
 */
import { lazyScene } from './kit.js';

lazyScene('[data-cta-scene]', (THREE, el) => import('./cta-scene.js').then((m) => m.default(THREE, el)));
lazyScene('[data-process-scene]', (THREE, el) => import('./process-scene.js').then((m) => m.default(THREE, el)));
// The home page's market globe reuses the International page scene.
lazyScene('[data-globe][data-lazy]', () => import('./globe.js'));
