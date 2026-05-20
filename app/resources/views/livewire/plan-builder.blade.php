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
                Matching food, laundry, souvenirs &amp; experiences to your budget.
            </p>
        @elseif($islandName)
            <p class="text-sm text-muraka-600 mt-1">{{ $islandName }} · island services only</p>
        @endif
    </div>

    <div class="sticky top-[4.25rem] z-30 -mx-6 sm:-mx-10 lg:-mx-16 px-6 sm:px-10 lg:px-16 py-3 bg-moodhu-50/95 backdrop-blur border-b border-moodhu-200 mb-6">
        <div class="card p-4 sm:p-5 max-w-4xl mx-auto">
            <form wire:submit.prevent="generate" class="space-y-4" x-data="{ budget: @entangle('budgetUsd').live }">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <x-ui.budget-controls
                            :presets="[500, 2000, 5000]"
                            :disabled="$isGenerating"
                        />
                    </div>
                    <div class="sm:col-span-2 sm:max-w-xs">
                        <label for="plan-days" class="block text-xs font-semibold uppercase tracking-label text-muraka-500 mb-1">Days</label>
                        <select id="plan-days" wire:model="days" class="input-field w-full" @disabled($isGenerating)>
                            @foreach([3, 5, 7] as $d)
                                <option value="{{ $d }}">{{ $d }} days</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($hasPlan)
                    <x-ui.budget-bar :spent="$spentUsd" :total="$budgetUsd" />
                @endif

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

                @if($error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endif

                @if(! $hasPlan && ! $isGenerating)
                    <p class="text-[11px] text-muraka-500">
                        @auth
                            Set your budget and days, then generate — your itinerary will appear below.
                        @else
                            Log in to generate a personalized plan with AI.
                        @endauth
                    </p>
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
        @if($summary)
            <p class="text-sm text-muraka-700 max-w-3xl mb-6 leading-relaxed">{{ $summary }}</p>
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
                        timeline
                    />
                @endforeach
            </div>

            <div class="max-w-3xl mx-auto mt-8 card p-5">
                <x-ui.budget-bar :spent="$spentUsd" :total="$budgetUsd" class="mb-3" />
                <p class="text-[11px] text-muraka-400">
                    Flights and guesthouse stays not included. Book each item separately — funds held in escrow until you redeem your QR voucher.
                </p>
            </div>
        @endif
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
