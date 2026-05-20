@props([
    'island' => null,
])

@php
    $budgetOptions = [500, 2000, 5000];
    $dayOptions = [3, 5, 7];
@endphp

<section
    class="mb-8 rounded-3xl border border-moodhu-200 bg-gradient-to-br from-moodhu-50 via-white to-madi-50/30 p-5 sm:p-6 lg:p-8 shadow-card ring-1 ring-madi-100/50"
    aria-labelledby="ai-planner-heading"
    x-data="{
        budgetChosen: false,
        budget: null,
        days: 5,
        pickBudget(amount) {
            this.budget = amount;
            this.budgetChosen = true;
        },
        isBuilding: false,
        planUrl() {
            const params = new URLSearchParams({
                budget: this.budget,
                days: this.days,
                generate: '1',
            });
            return '{{ route('plan') }}?' + params.toString();
        },
        buildPlan() {
            if (this.isBuilding || this.budget === null) {
                return;
            }
            this.isBuilding = true;
            try {
                sessionStorage.setItem('plannerBuilding', '1');
            } catch (e) {}
            window.location.assign(this.planUrl());
        }
    }"
>
    <p class="section-eyebrow mb-1">AI-powered</p>
    <h2 id="ai-planner-heading" class="section-title mb-1">Plan your island stay</h2>
    <p class="text-sm text-muraka-600 mb-5 max-w-xl">
        Food, laundry, souvenirs &amp; experiences — matched to your budget on {{ $island?->name ?? 'your island' }}.
    </p>

    {{-- Step 1: pick a budget (always visible) --}}
    <div class="mb-1">
        <x-ui.budget-presets
            :presets="$budgetOptions"
            pick-action="pickBudget(%d)"
            class="gap-3"
        />
    </div>
    <p class="text-xs text-muraka-500 mb-4" x-show="!budgetChosen">
        Tap an amount to set your budget and continue.
    </p>

    {{-- Step 2: prompt, slider, days, CTA (after budget selected) --}}
    <div
        x-show="budgetChosen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="space-y-4 border-t border-moodhu-200/80 pt-5"
    >
        <div class="flex flex-col lg:flex-row gap-4">
            <div class="planner-prompt flex flex-1 items-center gap-4 min-w-0" aria-live="polite">
                <div class="planner-prompt-icon" aria-hidden="true">
                    <x-icons.icon name="sparkles" class="w-6 h-6" />
                </div>
                <p class="relative z-10 min-w-0 flex-1 leading-tight">
                    <span class="text-base sm:text-lg text-muraka-600">I have</span>
                    <span
                        class="inline-block mx-1 text-3xl sm:text-4xl font-bold tracking-tight text-madi-700 tabular-nums"
                        x-text="'$' + budget.toLocaleString()"
                    ></span>
                    <span class="text-base sm:text-lg text-muraka-600">for</span>
                    <span
                        class="inline-block mx-1 text-2xl sm:text-3xl font-bold text-muraka-900 tabular-nums"
                        x-text="days"
                    ></span>
                    <span class="text-lg sm:text-xl font-semibold text-muraka-800">days</span>
                    <span class="mt-2 block text-base sm:text-lg font-semibold text-madi-800">
                        — plan my stay
                    </span>
                </p>
            </div>

            <button
                type="button"
                @click="buildPlan()"
                :disabled="isBuilding"
                class="btn-primary shrink-0 self-stretch lg:self-center py-4 px-8 text-base sm:text-lg font-bold shadow-md hover:shadow-lg lg:min-w-[11rem] flex items-center justify-center gap-2 disabled:opacity-80 disabled:cursor-wait"
                :aria-busy="isBuilding"
            >
                <span class="inline-flex items-center justify-center gap-2" x-show="!isBuilding" x-cloak>
                    <x-icons.icon name="sparkles" class="w-5 h-5" />
                    Build my plan
                </span>
                <span class="inline-flex items-center justify-center gap-2" x-show="isBuilding" x-cloak>
                    <span class="planner-btn-spinner" aria-hidden="true"></span>
                    Building your plan…
                </span>
            </button>
        </div>

        <x-ui.budget-slider id="home-budget-slider" />

        <div class="flex flex-wrap gap-2">
            <span class="text-[10px] uppercase tracking-label text-muraka-500 w-full sm:w-auto sm:mr-1 self-center">Days</span>
            @foreach($dayOptions as $d)
                <button
                    type="button"
                    @click="days = {{ $d }}"
                    :class="days === {{ $d }} ? 'planner-chip-active' : 'planner-chip'"
                >
                    {{ $d }} days
                </button>
            @endforeach
        </div>

        <p class="text-[11px] text-muraka-400">
            Flights and guesthouse stays not included — island services only. Your itinerary appears after AI builds it.
        </p>
    </div>
</section>
