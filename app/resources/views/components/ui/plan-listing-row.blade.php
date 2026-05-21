@props([
    'listing' => [],
    'note' => null,
    'showBook' => false,
    'showAsk' => false,
    'compact' => false,
])

@php
    $listingId = $listing['id'] ?? null;
    $slug = $listing['slug'] ?? '';
    $title = $listing['title'] ?? 'Listing';
    $imageUrl = $listing['image_url'] ?? null;
    $category = $listing['category'] ?? 'experience';
    $priceUsd = (float) ($listing['price_usd'] ?? 0);
    $priceMvr = (float) ($listing['price_mvr'] ?? 0);
    $listingUrl = $slug ? route('listing', $slug) : '#';
    $checkoutUrl = $slug && $showBook ? route('checkout', $slug) : null;
@endphp

<div {{ $attributes->merge(['class' => 'flex gap-3 items-center']) }}>
    <a href="{{ $listingUrl }}" class="shrink-0 overflow-hidden rounded-xl bg-moodhu-100 {{ $compact ? 'w-14 h-14' : 'w-16 h-16' }}">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" />
        @else
            <div class="flex h-full w-full items-center justify-center bg-moodhu-200">
                <x-icons.category :category="$category" class="w-6 h-6 text-muraka-400" />
            </div>
        @endif
    </a>

    <div class="min-w-0 flex-1">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                @if($note)
                    <p class="text-[10px] font-semibold uppercase tracking-label text-madi-600 mb-0.5">{{ $note }}</p>
                @endif
                <a href="{{ $listingUrl }}" class="font-medium text-sm text-muraka-900 hover:text-madi-700 line-clamp-2 leading-snug transition-colors duration-150">
                    {{ $title }}
                </a>
            </div>
            <div class="text-right shrink-0">
                <x-ui.price :usd="$priceUsd" :mvr="$priceMvr" size="sm" />
            </div>
        </div>
    </div>

    <div class="flex shrink-0 flex-col gap-1.5 sm:flex-row sm:items-center">
        @if($showAsk && $listingId)
            @auth
                <button
                    type="button"
                    onclick="Livewire.dispatch('ask-about-listing', { listingId: {{ $listingId }} })"
                    title="Ask the concierge about this"
                    aria-label="Ask the concierge about {{ $title }}"
                    class="inline-flex items-center justify-center rounded-full bg-white border border-moodhu-200 p-2 text-iru-700 shadow-sm transition-all duration-150 hover:border-madi-300 hover:bg-iru-50 hover:text-iru-800 focus:outline-none focus:ring-2 focus:ring-madi-400 focus:ring-offset-2"
                >
                    <x-icons.icon name="ask-ai" class="h-4 w-4" />
                </button>
            @endauth
        @endif
        @if($checkoutUrl)
            <a href="{{ $checkoutUrl }}" class="btn-secondary text-xs px-3 py-2 text-center">Book</a>
        @endif
    </div>
</div>
