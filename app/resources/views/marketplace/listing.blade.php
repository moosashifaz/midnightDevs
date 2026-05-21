<x-marketplace-layout>
    <div class="page-wrap max-w-7xl mx-auto">
    <a href="{{ route('category', $listing->category) }}" class="inline-flex items-center gap-1 text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150 mb-3">
        <x-icons.icon name="chevron-left" class="w-4 h-4" />
        Back to {{ $listing->categoryLabel() }}
    </a>

    <article class="grid lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3">
            <x-listing-image :listing="$listing" class="aspect-[4/3] sm:aspect-[16/10] rounded-3xl shadow-card" />

            <div class="mt-6">
                <h1 class="text-3xl font-bold text-muraka-900">{{ $listing->title }}</h1>
                <p class="text-sm text-muraka-500 mt-1 flex flex-wrap items-center gap-1">
                    By <span class="text-madi-600 font-medium">{{ $listing->provider->business_name }}</span>
                    · {{ $listing->island->name }} ·
                    <span class="inline-flex items-center gap-0.5 text-iru-600 font-medium">
                        <x-icons.icon name="star" class="w-3.5 h-3.5 fill-iru-400 stroke-iru-500" />
                        {{ number_format($listing->rating, 1) }}
                    </span>
                    <span class="text-muraka-400">({{ $listing->review_count }} reviews)</span>
                </p>

                <div class="mt-4 text-muraka-700 leading-relaxed">
                    <p>{{ $listing->description }}</p>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <x-ui.card class="p-3 text-center">
                        <div class="text-[10px] uppercase tracking-label text-muraka-500">Type</div>
                        <div class="font-semibold mt-1 capitalize text-muraka-900">{{ $listing->type }}</div>
                    </x-ui.card>
                    <x-ui.card class="p-3 text-center">
                        <div class="text-[10px] uppercase tracking-label text-muraka-500">Lead time</div>
                        <div class="font-semibold mt-1 text-muraka-900">{{ $listing->lead_time_minutes > 0 ? $listing->lead_time_minutes.' min' : 'Instant' }}</div>
                    </x-ui.card>
                    <x-ui.card class="p-3 text-center">
                        <div class="text-[10px] uppercase tracking-label text-muraka-500">Orders</div>
                        <div class="font-semibold mt-1 text-muraka-900">{{ $listing->order_count }}</div>
                    </x-ui.card>
                </div>
            </div>
        </div>

        <aside class="lg:col-span-2 lg:sticky lg:top-20 self-start space-y-4">
            <x-ui.card class="p-5">
                <div class="text-3xl font-bold text-madi-700">MVR {{ number_format($listing->price_mvr, 2) }}</div>
                <div class="text-sm text-muraka-500">≈ USD {{ number_format($listing->price_usd, 2) }}</div>

                <div class="text-xs text-muraka-500 mt-2 space-y-0.5">
                    <div class="flex justify-between"><span>Base price</span><span>MVR {{ number_format($listing->price_mvr / 1.16, 2) }}</span></div>
                    <div class="flex justify-between"><span>TGST (16%)</span><span>MVR {{ number_format($listing->price_mvr - ($listing->price_mvr / 1.16), 2) }}</span></div>
                </div>

                <div class="mt-5">
                    @auth
                        <x-ui.button variant="primary" :href="route('checkout', $listing)" class="w-full py-3 text-base">
                            {{ $listing->type === 'instant' ? 'Buy now' : 'Reserve & pay' }}
                        </x-ui.button>

                        <button type="button"
                                onclick="Livewire.dispatch('ask-about-listing', { listingId: {{ $listing->id }} })"
                                class="group relative mt-3 w-full overflow-hidden rounded-xl bg-gradient-to-r from-iru-500 via-iru-400 to-dhooni-400 px-4 py-3 text-sm font-semibold text-muraka-900 shadow-card transition-all duration-200 hover:shadow-card-hover focus:outline-none focus:ring-2 focus:ring-iru-400 focus:ring-offset-2">
                            <span class="absolute inset-0 bg-white/30 opacity-0 transition-opacity duration-200 group-hover:opacity-100"></span>
                            <span aria-hidden="true" class="absolute -top-6 -right-6 h-16 w-16 rounded-full bg-white/20 blur-2xl"></span>
                            <span class="relative flex items-center justify-center gap-2">
                                <x-icons.icon name="ask-ai" class="h-5 w-5 text-muraka-900" />
                                Ask the concierge about this
                            </span>
                        </button>
                    @else
                        <x-ui.button variant="primary" :href="route('checkout', $listing)" class="w-full py-3 text-base">
                            {{ $listing->type === 'instant' ? 'Buy now' : 'Reserve & pay' }}
                        </x-ui.button>
                    @endauth
                </div>

                <div class="text-[11px] text-muraka-500 mt-3 text-center flex items-center justify-center gap-1">
                    <x-icons.icon name="credit-card" class="w-3.5 h-3.5" />
                    Pay via <span class="font-mono text-madi-700">BML Swipe</span>.
                </div>
            </x-ui.card>

            <div class="rounded-2xl bg-madi-50 border border-madi-200 p-4 text-xs text-muraka-800">
                <div class="font-semibold mb-1 flex items-center gap-1.5 text-madi-800">
                    <x-icons.icon name="shield-check" class="w-4 h-4 text-madi-600" />
                    Verified provider
                </div>
                <p>{{ $listing->provider->business_name }} is verified by AfterArrival. Reviews from completed orders only.</p>
            </div>
        </aside>
    </article>

    @if($related->isNotEmpty())
        <section class="mt-20">
            <h2 class="section-title mb-3">More in {{ $listing->categoryLabel() }}</h2>
            <div class="grid sm:grid-cols-4 gap-4">
                @foreach($related as $r)
                    <x-ui.listing-card :listing="$r" />
                @endforeach
            </div>
        </section>
    @endif
    </div>
</x-marketplace-layout>
