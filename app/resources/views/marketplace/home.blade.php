<x-marketplace-layout>
    {{-- Full-bleed hero: edge-to-edge video, taller banner, text block centered with left-aligned copy --}}
    <section class="relative w-full min-h-[420px] sm:min-h-[520px] lg:min-h-[640px] overflow-hidden" aria-label="Welcome banner">
        {{-- Poster fallback for reduced motion and while video loads --}}
        <img
            src="/images/categories/experience.webp"
            alt=""
            aria-hidden="true"
            class="hero-video-fallback hero-media absolute inset-0 h-full w-full object-cover"
        />

        <video
            class="hero-video hero-media absolute inset-0 h-full w-full object-cover min-w-full min-h-full"
            autoplay
            muted
            loop
            playsinline
            poster="/images/listings/snorkel-with-sea-turtles.webp"
            aria-hidden="true"
        >
            @php
                $heroVideoPath = public_path('videos/hero-water-sports.mp4');
                $heroVideoVersion = file_exists($heroVideoPath) ? filemtime($heroVideoPath) : time();
            @endphp
            <source src="/videos/hero-water-sports.mp4?v={{ $heroVideoVersion }}" type="video/mp4">
        </video>

        <div class="absolute inset-0 bg-gradient-to-b from-muraka-900/50 via-muraka-900/35 to-muraka-900/65"></div>

        <div class="relative z-10 flex min-h-[inherit] items-center justify-center px-6 sm:px-10 lg:px-16 py-16 sm:py-20">
            <div class="w-full max-w-2xl text-left">
                <p class="text-xs font-semibold uppercase tracking-label text-moodhu-200">{{ $island?->atoll }} Atoll</p>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mt-2 mb-3 text-white leading-tight">
                    @if($island) Welcome to {{ $island->name }}.
                    @else Welcome.
                    @endif
                </h1>
                <p class="text-moodhu-100 text-sm sm:text-base lg:text-lg leading-relaxed max-w-xl">
                    Find food, laundry, souvenirs, and experiences from local providers on your island. Pay digitally, scan to redeem.
                </p>

                @if($islands->count() > 1)
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur rounded-full px-3 py-1.5 text-sm text-white mt-6">
                        <x-icons.icon name="map-pin" class="w-4 h-4 text-moodhu-200" />
                        <span class="opacity-90">You're on:</span>
                        <strong>{{ $island?->name }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="w-full px-6 sm:px-10 lg:px-16 py-8">
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
            @foreach($categories as $cat)
                <x-ui.category-tile
                    :category="$cat['key']"
                    :label="$cat['label']"
                    :count="$cat['count']"
                    :href="route('category', $cat['key'])"
                />
            @endforeach
        </section>

        <section class="mb-8">
            <div class="flex items-end justify-between mb-3">
                <h2 class="section-title">Featured on {{ $island?->name ?? 'your island' }}</h2>
                <a href="{{ route('category', 'experience') }}" class="text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150 flex items-center gap-0.5">
                    See all <x-icons.icon name="chevron-right" class="w-4 h-4" />
                </a>
            </div>

            @if($featured->isEmpty())
                <x-ui.card class="p-6 text-center text-muraka-500">No listings yet for this island. Check back soon.</x-ui.card>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($featured as $listing)
                        <x-ui.listing-card :listing="$listing" />
                    @endforeach
                </div>
            @endif
        </section>

        <section class="card p-5 sm:p-6">
            <h3 class="font-bold mb-4 flex items-center gap-2 text-muraka-900">
                <x-icons.icon name="sparkles" class="w-5 h-5 text-iru-500" />
                How AfterArrival works
            </h3>
            <ol class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-muraka-700">
                <li class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-madi-100 text-madi-700 font-bold text-xs">1</span>
                    <span><strong class="text-muraka-900">Browse</strong> local providers verified on your island.</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-madi-100 text-madi-700 font-bold text-xs">2</span>
                    <span><strong class="text-muraka-900">Pay</strong> digitally via <span class="font-mono text-madi-700">BML Swipe</span>. Funds held in escrow.</span>
                </li>
                <li class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-madi-100 text-madi-700 font-bold text-xs">3</span>
                    <span class="flex items-start gap-1"><x-icons.icon name="qr-code" class="w-4 h-4 mt-0.5 text-madi-600 shrink-0" /><span><strong class="text-muraka-900">Scan</strong> your QR voucher at pickup.</span></span>
                </li>
                <li class="flex gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-madi-100 text-madi-700 font-bold text-xs">4</span>
                    <span><strong class="text-muraka-900">Review</strong> after fulfillment — funds release to the provider.</span>
                </li>
            </ol>
        </section>
    </div>
</x-marketplace-layout>
