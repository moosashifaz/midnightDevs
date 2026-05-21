@props([
    'days' => 5,
])

@php
    $lottieSrc = asset('animations/AfterArrival-Logo-Animation.json');
@endphp

<div
    class="planner-building-overlay fixed inset-0 z-50 flex flex-col items-center justify-center px-6 py-10"
    role="dialog"
    aria-modal="true"
    aria-busy="true"
    aria-labelledby="planner-building-title"
    aria-live="polite"
    x-data="{
        activeStep: 0,
        messageIndex: 0,
        steps: [
            'Reading your interests',
            'Matching live listings',
            'Balancing your budget',
            'Building your days',
        ],
        messages: [
            'Scanning what you want to do on the island…',
            'Finding real listings that fit your activities…',
            'Staying within your budget in USD…',
            'Lining up your day-by-day itinerary…',
        ],
        timer: null,
        start() {
            this.timer = setInterval(() => {
                this.activeStep = (this.activeStep + 1) % this.steps.length;
                this.messageIndex = (this.messageIndex + 1) % this.messages.length;
            }, 2500);
        },
        stop() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
    }"
    x-init="start()"
    x-on:destroy="stop()"
>
    <div class="planner-building-panel relative w-full max-w-md text-center pt-4">
        <div class="planner-building-glow" aria-hidden="true"></div>

        <div
            wire:ignore
            data-planner-lottie
            data-lottie-src="{{ $lottieSrc }}"
            class="planner-lottie-player relative z-10 mx-auto"
            aria-hidden="true"
        ></div>

        <h2 id="planner-building-title" class="relative z-10 mt-4 text-xl sm:text-2xl font-bold text-muraka-900">
            Creating your island plan
        </h2>
        <p class="relative z-10 mt-1 text-sm text-madi-700 font-medium">
            {{ $days }} {{ $days === 1 ? 'day' : 'days' }} · AI island planner
        </p>

        <p
            class="planner-message-fade relative z-10 mt-4 min-h-[1.25rem] text-sm text-muraka-600"
            x-text="messages[messageIndex]"
        ></p>

        <ol class="relative z-10 mt-6 space-y-2 text-left max-w-xs mx-auto">
            <template x-for="(step, index) in steps" :key="step">
                <li
                    class="flex items-center gap-2.5 text-sm transition-colors duration-300"
                    :class="{
                        'planner-building-step-active text-madi-800 font-semibold': activeStep === index,
                        'planner-building-step-done text-ruh-700': activeStep > index,
                        'text-muraka-400': activeStep < index,
                    }"
                >
                    <span
                        class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 text-[10px]"
                        :class="activeStep > index
                            ? 'border-ruh-500 bg-ruh-500 text-white'
                            : (activeStep === index ? 'border-madi-500 bg-madi-100 text-madi-700' : 'border-moodhu-300 bg-white')"
                        aria-hidden="true"
                    >
                        <span x-show="activeStep > index" x-cloak>✓</span>
                        <span x-show="activeStep === index" x-cloak class="block h-1.5 w-1.5 rounded-full bg-madi-600"></span>
                    </span>
                    <span x-text="step"></span>
                </li>
            </template>
        </ol>
    </div>

    <div class="absolute bottom-0 left-0 right-0 px-6 pb-8 pt-4 max-w-lg mx-auto w-full opacity-60" aria-hidden="true">
        <div class="space-y-2">
            <div class="planner-skeleton planner-skeleton-shimmer h-3 w-24"></div>
            <div class="planner-skeleton planner-skeleton-shimmer h-14 w-full rounded-2xl"></div>
            <div class="planner-skeleton planner-skeleton-shimmer h-14 w-full rounded-2xl"></div>
        </div>
    </div>
</div>
