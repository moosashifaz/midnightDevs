@props(['listing', 'href' => null])

@php
$url = $href ?? route('listing', $listing);
@endphp

<div class="card-interactive group relative overflow-hidden">
    <a href="{{ $url }}" class="block">
        <x-listing-image :listing="$listing" class="aspect-[16/10]" />
    </a>

    @auth
        <button type="button"
                onclick="Livewire.dispatch('ask-about-listing', { listingId: {{ $listing->id }} })"
                title="Ask the concierge about this"
                aria-label="Ask the concierge about {{ $listing->title }}"
                class="absolute top-2.5 right-2.5 z-10 inline-flex items-center justify-center rounded-full bg-white/95 p-2 text-iru-700 shadow-card backdrop-blur transition-all duration-150 hover:scale-110 hover:bg-iru-100 hover:text-iru-800 focus:outline-none focus:ring-2 focus:ring-iru-400 focus:ring-offset-2">
            <x-icons.icon name="ask-ai" class="h-4 w-4" />
        </button>
    @endauth

    <a href="{{ $url }}" class="block">
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
            <x-ui.price :usd="$listing->price_usd" :mvr="$listing->price_mvr" />
            @if($listing->lead_time_minutes > 0)
                <x-ui.badge variant="lead">{{ $listing->lead_time_minutes }}m lead</x-ui.badge>
            @else
                <x-ui.badge variant="instant">Instant</x-ui.badge>
            @endif
        </div>
    </div>
    </a>
</div>
