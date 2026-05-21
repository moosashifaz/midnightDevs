<div
    class="page-wrap"
    @if($autoGenerate) wire:init="runQueuedGenerate" @endif
    x-data
    x-init="
        try { sessionStorage.removeItem('plannerBuilding'); } catch (e) {}
    "
>
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150 mb-4" @if($isGenerating) tabindex="-1" aria-disabled="true" @endif>
            <x-icons.icon name="chevron-right" class="w-4 h-4 rotate-180" />
            Back to home
        </a>
        <p class="section-eyebrow mb-1">AI island planner</p>
        <h1 class="text-2xl sm:text-3xl font-bold text-muraka-900">
            @if($isGenerating)
                Building your {{ $days }}-day plan…
            @elseif($hasPlan)
                Your {{ $days }}-day plan
            @else
                Build your {{ $days }}-day plan
            @endif
        </h1>
        @if($isGenerating)
            <p class="text-sm text-madi-700 mt-1 flex items-center gap-2">
                <span class="planner-btn-spinner shrink-0" aria-hidden="true"></span>
                Matching your selected activities to live listings and your budget.
            </p>
        @elseif($islandName)
            <p class="text-sm text-muraka-600 mt-1">{{ $islandName }} · island services only</p>
        @endif
    </div>

    <div @class([
        'mb-6 max-w-4xl mx-auto',
        'sticky top-[4.25rem] z-30 -mx-6 sm:-mx-10 lg:-mx-16 px-6 sm:px-10 lg:px-16 py-3 bg-moodhu-50/95 backdrop-blur border-b border-moodhu-200' => ! $hasPlan && ! $isGenerating,
    ])>
        <div class="card p-4 sm:p-5">
            <form wire:submit.prevent="generate" class="space-y-4" x-data="{ budget: @entangle('budgetUsd').live }">
                @if($hasPlan && ! $isGenerating)
                    {{-- Compact bar after plan exists — scrolls away so the itinerary stays visible --}}
                    <x-ui.budget-bar :spent="$spentUsd" :total="$budgetUsd" />

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        @auth
                            <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="generate">Regenerate with AI</span>
                                <span wire:loading wire:target="generate">Generating…</span>
                            </button>
                            <button type="button" wire:click="savePlan" class="btn-ghost" wire:loading.attr="disabled" wire:target="savePlan">
                                <span wire:loading.remove wire:target="savePlan">Save plan</span>
                                <span wire:loading wire:target="savePlan">Saving…</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">Log in to generate your plan</a>
                        @endauth
                    </div>

                    <details class="planner-settings-panel group rounded-xl border border-moodhu-200 bg-moodhu-50/50 open:bg-white">
                        <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-madi-700 hover:text-madi-800 flex items-center justify-between gap-2">
                            <span>Adjust budget &amp; activities</span>
                            <x-icons.icon name="chevron-right" class="w-4 h-4 shrink-0 transition-transform group-open:rotate-90" />
                        </summary>
                        <div class="space-y-4 border-t border-moodhu-200 px-4 py-4">
                            @include('livewire.partials.plan-builder-settings')
                        </div>
                    </details>
                @else
                    @include('livewire.partials.plan-builder-settings')

                    <div class="flex flex-wrap gap-2 pt-1">
                        @auth
                            <button type="submit" class="btn-primary" wire:loading.attr="disabled" @disabled($isGenerating)>
                                <span wire:loading.remove wire:target="generate">
                                    {{ $hasPlan ? 'Regenerate with AI' : 'Generate with AI' }}
                                </span>
                                <span wire:loading wire:target="generate">Generating…</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">Log in to generate your plan</a>
                        @endauth
                    </div>

                    @if(! $hasPlan && ! $isGenerating)
                        <p class="text-[11px] text-muraka-500">
                            @auth
                                Set your budget and activities, then generate — your itinerary appears below.
                            @else
                                Log in to generate a personalized plan with AI.
                            @endauth
                        </p>
                    @endif
                @endif

                @if(session('status'))
                    <p class="text-sm text-madi-700 font-medium">{{ session('status') }}</p>
                @endif

                @if($error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endif
            </form>
        </div>
    </div>

    @if($isGenerating)
        <div class="max-w-3xl mx-auto mb-6 rounded-2xl border border-madi-200 bg-madi-50/60 px-4 py-3 flex items-center gap-3" role="status" aria-live="polite">
            <span class="planner-btn-spinner shrink-0" aria-hidden="true"></span>
            <p class="text-sm font-medium text-madi-800">AI is building your island itinerary — this usually takes a few seconds.</p>
        </div>
        <div class="max-w-3xl mx-auto space-y-6" aria-busy="true" aria-label="Generating plan">
            @for($i = 0; $i < 3; $i++)
                <div class="space-y-3">
                    <div class="planner-skeleton h-4 w-32"></div>
                    <div class="planner-skeleton h-20 w-full"></div>
                    <div class="planner-skeleton h-20 w-full"></div>
                </div>
            @endfor
        </div>
    @elseif($hasPlan)
        <section class="max-w-3xl mx-auto scroll-mt-6" aria-label="Your itinerary">
        @if($summary)
            <p class="text-sm text-muraka-700 mb-6 leading-relaxed">{{ $summary }}</p>
        @endif

        @if(count($planDays) === 0)
            <x-ui.card class="p-8 text-center max-w-lg mx-auto">
                <x-icons.icon name="sparkles" class="w-10 h-10 text-madi-400 mx-auto mb-3" />
                <p class="text-muraka-700 font-medium mb-2">Could not build a plan</p>
                <p class="text-sm text-muraka-500 mb-4">No listings matched your budget on this island. Try a higher budget or browse categories.</p>
                <x-ui.button :href="route('home')">Browse home</x-ui.button>
            </x-ui.card>
        @else
            <div class="max-w-3xl mx-auto planner-timeline">
                @foreach($planDays as $dayBlock)
                    <x-ui.plan-day-section
                        :day="$dayBlock['day']"
                        :title="$dayBlock['title']"
                        :items="$dayBlock['items']"
                        :show-book="auth()->check()"
                        :show-ask="auth()->check()"
                        timeline
                    />
                @endforeach
            </div>

            <div class="mt-8 card p-5">
                <p class="text-[11px] text-muraka-400">
                    Flights and guesthouse stays not included. Book each item separately — funds held in escrow until you redeem your QR voucher.
                </p>
            </div>
        @endif
        </section>
    @else
        <x-ui.card class="p-8 text-center max-w-lg mx-auto">
            <x-icons.icon name="sparkles" class="w-10 h-10 text-madi-400 mx-auto mb-3" />
            <p class="text-muraka-700 font-medium mb-2">Ready when you are</p>
            <p class="text-sm text-muraka-500">
                @auth
                    Tap <strong>Generate with AI</strong> above to build your itinerary from live listings on this island.
                @else
                    Log in and generate your plan — results show here once AI finishes.
                @endauth
            </p>
        </x-ui.card>
    @endif
</div>
