<x-marketplace-layout :title="'Saved plan — AfterArrival'">
    <div class="page-wrap">
        <div class="mb-6">
            <a href="{{ route('plans.index') }}" class="inline-flex items-center gap-1 text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150 mb-4">
                <x-icons.icon name="chevron-right" class="w-4 h-4 rotate-180" />
                My plans
            </a>
            <p class="section-eyebrow mb-1">Saved itinerary</p>
            <h1 class="section-title">{{ $savedPlan->titleLine() }}</h1>
            @if($savedPlan->summary)
                <p class="text-sm text-muraka-700 mt-2 leading-relaxed max-w-2xl">{{ $savedPlan->summary }}</p>
            @endif
            <p class="text-xs text-muraka-500 mt-2">Saved {{ $savedPlan->created_at->format('M j, Y g:i A') }}</p>
        </div>

        <div class="card p-4 sm:p-5 max-w-3xl mb-6">
            <x-ui.budget-bar :spent="$savedPlan->spent_usd" :total="$savedPlan->budget_usd" />
        </div>

        @if(empty($savedPlan->plan_data))
            <x-ui.card class="p-6 text-center text-muraka-500">This plan has no items.</x-ui.card>
        @else
            <div class="max-w-3xl mx-auto planner-timeline">
                @foreach($savedPlan->plan_data as $dayBlock)
                    <x-ui.plan-day-section
                        :day="$dayBlock['day'] ?? 1"
                        :title="$dayBlock['title'] ?? 'Island day'"
                        :items="$dayBlock['items'] ?? []"
                        :show-book="true"
                        :show-ask="true"
                        timeline
                    />
                @endforeach
            </div>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            @if(! empty($savedPlan->plan_data))
                <x-ui.button variant="primary" :href="route('plans.book-all', $savedPlan)">
                    Book all
                </x-ui.button>
            @endif
            <x-ui.button :href="route('plan', ['budget' => $savedPlan->budget_usd, 'days' => $savedPlan->days])">
                Build a new plan
            </x-ui.button>
            <x-ui.button variant="ghost" :href="route('home')">Browse home</x-ui.button>
        </div>
    </div>
</x-marketplace-layout>
