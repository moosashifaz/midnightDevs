<x-marketplace-layout>
    <a href="{{ route('category', $listing->category) }}" class="text-sm text-cyan-600 hover:underline">← Back to {{ $listing->categoryLabel() }}</a>

    <article class="grid lg:grid-cols-5 gap-6 mt-3">
        <div class="lg:col-span-3">
            <div class="aspect-[4/3] sm:aspect-[16/10] bg-gradient-to-br from-cyan-100 via-blue-100 to-indigo-100 dark:from-cyan-900 dark:via-blue-900 dark:to-indigo-900 rounded-3xl flex items-center justify-center text-8xl shadow-inner">
                @switch($listing->category)
                    @case('eat') 🍽️ @break
                    @case('wash') 🧺 @break
                    @case('buy') 🛍️ @break
                    @case('experience') ⛵ @break
                @endswitch
            </div>

            <div class="mt-6">
                <h1 class="text-3xl font-bold">{{ $listing->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    By <a href="#" class="text-cyan-600 hover:underline">{{ $listing->provider->business_name }}</a>
                    · {{ $listing->island->name }} ·
                    <span class="text-amber-600">★ {{ number_format($listing->rating, 1) }}</span>
                    <span class="text-gray-400">({{ $listing->review_count }} reviews)</span>
                </p>

                <div class="mt-4 prose prose-sm dark:prose-invert max-w-none">
                    <p>{{ $listing->description }}</p>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3 text-center">
                        <div class="text-[10px] uppercase tracking-wider text-gray-500">Type</div>
                        <div class="font-semibold mt-1 capitalize">{{ $listing->type }}</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3 text-center">
                        <div class="text-[10px] uppercase tracking-wider text-gray-500">Lead time</div>
                        <div class="font-semibold mt-1">{{ $listing->lead_time_minutes > 0 ? $listing->lead_time_minutes.' min' : 'Instant' }}</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3 text-center">
                        <div class="text-[10px] uppercase tracking-wider text-gray-500">Orders</div>
                        <div class="font-semibold mt-1">{{ $listing->order_count }}</div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="lg:col-span-2 lg:sticky lg:top-20 self-start">
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                <div class="text-3xl font-bold text-cyan-700 dark:text-cyan-300">MVR {{ number_format($listing->price_mvr, 2) }}</div>
                <div class="text-sm text-gray-500">≈ USD {{ number_format($listing->price_usd, 2) }}</div>

                <div class="text-xs text-gray-500 mt-2 space-y-0.5">
                    <div class="flex justify-between"><span>Base price</span><span>MVR {{ number_format($listing->price_mvr / 1.16, 2) }}</span></div>
                    <div class="flex justify-between"><span>TGST (16%)</span><span>MVR {{ number_format($listing->price_mvr - ($listing->price_mvr / 1.16), 2) }}</span></div>
                </div>

                <div class="mt-5">
                    @auth
                        <a href="{{ route('checkout', $listing) }}" class="block w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white text-center font-semibold py-3 rounded-xl shadow-lg shadow-cyan-500/30">
                            {{ $listing->type === 'instant' ? 'Buy now' : 'Reserve & pay' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white text-center font-semibold py-3 rounded-xl shadow-lg shadow-cyan-500/30">
                            Log in to book
                        </a>
                    @endauth
                </div>

                <div class="text-[11px] text-gray-500 mt-3 text-center">
                    Pay via <span class="font-mono text-cyan-700 dark:text-cyan-300">BML Swipe</span>. Funds held in escrow until you scan your voucher.
                </div>
            </div>

            <div class="rounded-2xl bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-800 mt-4 p-4 text-xs text-cyan-900 dark:text-cyan-100">
                <div class="font-semibold mb-1">Verified provider</div>
                <p>{{ $listing->provider->business_name }} is verified by AfterArrival. Reviews collected from real, completed orders.</p>
            </div>
        </aside>
    </article>

    @if($related->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-lg font-bold mb-3">More in {{ $listing->categoryLabel() }}</h2>
            <div class="grid sm:grid-cols-3 gap-4">
                @foreach($related as $r)
                    <a href="{{ route('listing', $r) }}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition">
                        <div class="font-medium text-sm">{{ $r->title }}</div>
                        <div class="text-xs text-gray-500 mt-1">MVR {{ number_format($r->price_mvr, 0) }} · ★ {{ number_format($r->rating, 1) }}</div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-marketplace-layout>
