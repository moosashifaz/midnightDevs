@props(['listing', 'href' => null])

@php
$url = $href ?? route('listing', $listing);
@endphp

<a href="{{ $url }}" class="card-interactive group block overflow-hidden">
    <x-listing-image :listing="$listing" class="aspect-[16/10]" />
    <div class="p-4">
        <div class="flex items-start justify-between gap-2 mb-1">
            <h3 class="font-semibold leading-tight text-muraka-900 group-hover:text-madi-700 transition-colors duration-150">{{ $listing->title }}</h3>
            <span class="flex items-center gap-0.5 text-xs text-iru-600 whitespace-nowrap font-medium">
                <x-icons.icon name="star" class="w-3.5 h-3.5 fill-iru-400 stroke-iru-500" />
                {{ number_format($listing->rating, 1) }}
            </span>
        </div>
        @if(isset($showProvider) && $showProvider)
            <p class="text-xs text-muraka-500 mb-3">{{ $listing->provider->business_name }} · {{ $listing->categoryLabel() }}</p>
        @else
            <p class="text-xs text-muraka-500 mb-3">{{ $listing->provider->business_name }}</p>
        @endif
        @if($slot->isNotEmpty())
            {{ $slot }}
        @endif
        <div class="flex items-end justify-between mt-2">
            <div>
                <div class="text-lg font-bold text-madi-700">MVR {{ number_format($listing->price_mvr, 0) }}</div>
                <div class="text-[11px] text-muraka-500">≈ USD {{ number_format($listing->price_usd, 2) }}</div>
            </div>
            @if($listing->lead_time_minutes > 0)
                <x-ui.badge variant="lead">{{ $listing->lead_time_minutes }}m lead</x-ui.badge>
            @else
                <x-ui.badge variant="instant">Instant</x-ui.badge>
            @endif
        </div>
    </div>
</a>
