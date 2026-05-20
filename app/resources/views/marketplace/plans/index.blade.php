<x-marketplace-layout title="My plans — AfterArrival">
    <div class="page-wrap">
        <div class="mb-6">
            <a href="{{ route('plan') }}" class="inline-flex items-center gap-1 text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150 mb-4">
                <x-icons.icon name="chevron-right" class="w-4 h-4 rotate-180" />
                Build a new plan
            </a>
            <p class="section-eyebrow mb-1">Saved itineraries</p>
            <h1 class="section-title">My plans</h1>
            <p class="text-sm text-muraka-600 mt-2">AI-generated island plans you saved for later.</p>
        </div>

        @if($plans->isEmpty())
            <x-ui.card class="p-8 text-center max-w-lg mx-auto">
                <x-icons.icon name="sparkles" class="w-10 h-10 text-madi-400 mx-auto mb-3" />
                <p class="text-muraka-700 font-medium mb-2">No saved plans yet</p>
                <p class="text-sm text-muraka-500 mb-4">Generate a plan on the home page, then tap Save plan.</p>
                <x-ui.button :href="route('home')">Go to home</x-ui.button>
            </x-ui.card>
        @else
            <div class="grid gap-3 max-w-2xl">
                @foreach($plans as $plan)
                    <a href="{{ route('plans.show', $plan) }}" class="card-interactive block p-4 sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-muraka-900">{{ $plan->titleLine() }}</p>
                                @if($plan->summary)
                                    <p class="text-sm text-muraka-600 mt-1 line-clamp-2">{{ $plan->summary }}</p>
                                @endif
                                <p class="text-xs text-muraka-500 mt-2">
                                    Saved {{ $plan->created_at->diffForHumans() }}
                                    · ${{ number_format((float) $plan->spent_usd, 0) }} planned
                                </p>
                            </div>
                            <x-icons.icon name="chevron-right" class="w-5 h-5 text-muraka-400 shrink-0 mt-0.5" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-marketplace-layout>
