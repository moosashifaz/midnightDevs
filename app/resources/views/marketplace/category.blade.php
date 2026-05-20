<x-marketplace-layout>
    <div class="mb-6">
        <p class="text-xs uppercase tracking-wider text-cyan-600 font-medium">{{ $island?->name ?? 'All islands' }}</p>
        <h1 class="text-2xl sm:text-3xl font-bold">{{ $label }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $listings->total() }} {{ Str::plural('listing', $listings->total()) }}</p>
    </div>

    @if($listings->isEmpty())
        <div class="rounded-2xl bg-gray-50 dark:bg-gray-800 p-10 text-center text-gray-500">
            Nothing here yet. Try another category or island.
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($listings as $listing)
                <a href="{{ route('listing', $listing) }}" class="group block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition">
                    <div class="aspect-[16/10] bg-gradient-to-br from-cyan-100 via-blue-100 to-indigo-100 dark:from-cyan-900 dark:via-blue-900 dark:to-indigo-900 flex items-center justify-center text-5xl">
                        @switch($listing->category)
                            @case('eat') 🍽️ @break
                            @case('wash') 🧺 @break
                            @case('buy') 🛍️ @break
                            @case('experience') ⛵ @break
                        @endswitch
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="font-semibold leading-tight group-hover:text-cyan-600 transition">{{ $listing->title }}</h3>
                            <span class="text-xs text-amber-600 whitespace-nowrap">★ {{ number_format($listing->rating, 1) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">{{ $listing->provider->business_name }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $listing->description }}</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <div class="text-lg font-bold text-cyan-700 dark:text-cyan-300">MVR {{ number_format($listing->price_mvr, 0) }}</div>
                                <div class="text-[11px] text-gray-500">≈ USD {{ number_format($listing->price_usd, 2) }}</div>
                            </div>
                            @if($listing->lead_time_minutes > 0)
                                <span class="text-[10px] bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full">{{ $listing->lead_time_minutes }}m lead</span>
                            @else
                                <span class="text-[10px] bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 px-2 py-0.5 rounded-full">Instant</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $listings->links() }}
        </div>
    @endif
</x-marketplace-layout>
