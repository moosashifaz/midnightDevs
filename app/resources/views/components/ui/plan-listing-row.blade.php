@props([
    'listing' => [],
    'note' => null,
    'showBook' => false,
    'compact' => false,
])

@php
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
                <div class="text-sm font-bold text-madi-700">${{ number_format($priceUsd, 0) }}</div>
                <div class="text-[10px] text-muraka-500">MVR {{ number_format($priceMvr, 0) }}</div>
            </div>
        </div>
    </div>

    @if($checkoutUrl)
        <a href="{{ $checkoutUrl }}" class="btn-secondary shrink-0 text-xs px-3 py-2">Book</a>
    @endif
</div>
