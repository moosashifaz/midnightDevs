<x-marketplace-layout>
    <section class="rounded-3xl bg-gradient-to-br from-cyan-500 via-blue-500 to-indigo-600 text-white p-6 sm:p-10 shadow-xl mb-8 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="relative">
            <p class="text-cyan-100 text-sm font-medium uppercase tracking-wider">{{ $island?->atoll }} Atoll</p>
            <h1 class="text-3xl sm:text-4xl font-bold mt-1 mb-2">
                @if($island) Welcome to {{ $island->name }}.
                @else Welcome.
                @endif
            </h1>
            <p class="text-cyan-50 max-w-xl mb-6">Find food, laundry, souvenirs, and experiences from local providers on your island. Pay digitally, scan to redeem.</p>

            @if($islands->count() > 1)
                <form method="POST" action="" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur rounded-full px-3 py-1 text-sm">
                    @csrf
                    <span class="opacity-80">📍</span>
                    <span>You're on:</span>
                    <strong>{{ $island?->name }}</strong>
                </form>
            @endif
        </div>
    </section>

    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        @foreach($categories as $cat)
            <a href="{{ route('category', $cat['key']) }}" class="group flex flex-col items-center p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition">
                <span class="text-3xl mb-2">
                    @switch($cat['key'])
                        @case('eat') 🍽️ @break
                        @case('wash') 🧺 @break
                        @case('buy') 🛍️ @break
                        @case('experience') ⛵ @break
                    @endswitch
                </span>
                <span class="font-semibold">{{ $cat['label'] }}</span>
                <span class="text-xs text-gray-500 mt-0.5">{{ $cat['count'] }} {{ Str::plural('listing', $cat['count']) }}</span>
            </a>
        @endforeach
    </section>

    <section class="mb-8">
        <div class="flex items-end justify-between mb-3">
            <h2 class="text-xl font-bold">Featured on {{ $island?->name ?? 'your island' }}</h2>
            <a href="{{ route('category', 'experience') }}" class="text-sm text-cyan-600 hover:underline">See all →</a>
        </div>

        @if($featured->isEmpty())
            <div class="rounded-2xl bg-gray-50 dark:bg-gray-800 p-6 text-center text-gray-500">No listings yet for this island. Check back soon.</div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($featured as $listing)
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
                            <p class="text-xs text-gray-500 mb-3">{{ $listing->provider->business_name }} · {{ $listing->categoryLabel() }}</p>
                            <div class="flex items-end justify-between">
                                <div>
                                    <div class="text-lg font-bold text-cyan-700 dark:text-cyan-300">MVR {{ number_format($listing->price_mvr, 0) }}</div>
                                    <div class="text-[11px] text-gray-500">≈ USD {{ number_format($listing->price_usd, 2) }}</div>
                                </div>
                                @if($listing->lead_time_minutes > 0)
                                    <span class="text-[10px] bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full">{{ $listing->lead_time_minutes }}m lead time</span>
                                @else
                                    <span class="text-[10px] bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 px-2 py-0.5 rounded-full">Instant</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 text-sm">
        <h3 class="font-bold mb-2 flex items-center gap-2">💡 How AfterArrival works</h3>
        <ol class="space-y-1.5 text-gray-700 dark:text-gray-300">
            <li><strong>1.</strong> Browse local providers verified on your island.</li>
            <li><strong>2.</strong> Pay digitally via <span class="font-mono text-cyan-700">BML Swipe</span>. Your funds are held in escrow.</li>
            <li><strong>3.</strong> Show your QR voucher when you collect your order or experience.</li>
            <li><strong>4.</strong> Funds release to the provider after fulfillment. Leave a review.</li>
        </ol>
    </section>
</x-marketplace-layout>
