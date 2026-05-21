import { destroyAllPlannerLotties, initAllPlannerLotties } from './planner-lottie';

// AfterArrival client bootstrap
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((err) => {
            console.warn('SW registration failed', err);
        });
    });
}

function bootPlannerLottie() {
    initAllPlannerLotties();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootPlannerLottie);
} else {
    bootPlannerLottie();
}

document.addEventListener('livewire:navigated', bootPlannerLottie);

document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morph.removed', ({ el }) => {
        destroyAllPlannerLotties(el);
    });

    window.Livewire.hook('morph.added', ({ el }) => {
        initAllPlannerLotties(el);
    });
});
