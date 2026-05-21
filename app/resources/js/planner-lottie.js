import lottie from 'lottie-web';

const instances = new WeakMap();

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export function initPlannerLottie(container) {
    if (!container || instances.has(container)) {
        return;
    }

    const src = container.dataset.lottieSrc;
    if (!src) {
        return;
    }

    const reduced = prefersReducedMotion();

    const animation = lottie.loadAnimation({
        container,
        renderer: 'svg',
        loop: !reduced,
        autoplay: !reduced,
        path: src,
    });

    if (reduced) {
        animation.addEventListener('DOMLoaded', () => {
            animation.goToAndStop(0, true);
        });
    }

    instances.set(container, animation);
}

export function destroyPlannerLottie(container) {
    const animation = instances.get(container);
    if (!animation) {
        return;
    }

    animation.destroy();
    instances.delete(container);
    container.replaceChildren();
}

const LOTTIE_SELECTOR = '[data-lottie-player], [data-planner-lottie]';

export function initAllPlannerLotties(root = document) {
    root.querySelectorAll(LOTTIE_SELECTOR).forEach(initPlannerLottie);
}

export function destroyAllPlannerLotties(root = document) {
    root.querySelectorAll(LOTTIE_SELECTOR).forEach(destroyPlannerLottie);
}
