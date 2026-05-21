@props([
    'listing' => [],
    'note' => null,
    'showBook' => false,
    'showAsk' => false,
    'editable' => false,
    'dayIndex' => 0,
    'itemIndex' => 0,
    'chooserOpen' => false,
    'chooserOptions' => [],
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
    $day = (int) $dayIndex;
    $item = (int) $itemIndex;
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <div class="flex gap-3 items-center">
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
            @if($editable)
                <button
                    type="button"
                    wire:click.stop="toggleChooseOptions({{ $day }}, {{ $item }})"
                    wire:loading.attr="disabled"
                    wire:target="toggleChooseOptions({{ $day }}, {{ $item }})"
                    @class([
                        'inline-flex items-center justify-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition-colors duration-150',
                        'border-madi-400 bg-madi-50 text-madi-800' => $chooserOpen,
                        'border-moodhu-200 bg-white text-madi-700 hover:border-madi-300 hover:bg-madi-50' => ! $chooserOpen,
                    ])
                    title="Pick from available listings"
                >
                    <span wire:loading.remove wire:target="toggleChooseOptions({{ $day }}, {{ $item }})">
                        {{ $chooserOpen ? 'Close' : 'Choose' }}
                    </span>
                    <span wire:loading wire:target="toggleChooseOptions({{ $day }}, {{ $item }})">…</span>
                </button>
                <button
                    type="button"
                    wire:click.stop="swapPlanItem({{ $day }}, {{ $item }})"
                    wire:loading.attr="disabled"
                    wire:target="swapPlanItem({{ $day }}, {{ $item }})"
                    class="inline-flex items-center justify-center rounded-lg border border-moodhu-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-madi-700 transition-colors duration-150 hover:border-madi-300 hover:bg-madi-50"
                    title="Try another listing in this category"
                >
                    <span wire:loading.remove wire:target="swapPlanItem({{ $day }}, {{ $item }})">Swap</span>
                    <span wire:loading wire:target="swapPlanItem({{ $day }}, {{ $item }})">…</span>
                </button>
                <button
                    type="button"
                    wire:click.stop="removePlanItem({{ $day }}, {{ $item }})"
                    wire:confirm="Remove this activity from your plan?"
                    wire:loading.attr="disabled"
                    wire:target="removePlanItem({{ $day }}, {{ $item }})"
                    class="inline-flex items-center justify-center rounded-lg border border-moodhu-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-muraka-600 transition-colors duration-150 hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                    title="Remove from plan"
                >
                    Remove
                </button>
            @endif
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

    @if($editable && $chooserOpen && count($chooserOptions) > 0)
        <div
            class="rounded-xl border border-madi-200 bg-madi-50/40 p-3 space-y-2"
            role="listbox"
            aria-label="Choose a listing for this activity"
        >
            <p class="text-[10px] font-semibold uppercase tracking-label text-madi-700">Pick an option</p>
            <ul class="space-y-1.5 max-h-56 overflow-y-auto">
                @foreach($chooserOptions as $option)
                    @php
                        $opt = $option['listing'] ?? [];
                        $optId = (int) ($opt['id'] ?? 0);
                        $isCurrent = (bool) ($option['is_current'] ?? false);
                        $optSlug = $opt['slug'] ?? '';
                        $optTitle = $opt['title'] ?? 'Listing';
                        $optUsd = (float) ($opt['price_usd'] ?? 0);
                        $optMvr = (float) ($opt['price_mvr'] ?? 0);
                    @endphp
                    <li wire:key="chooser-{{ $day }}-{{ $item }}-{{ $optId }}">
                        <button
                            type="button"
                            wire:click.stop="pickPlanItem({{ $day }}, {{ $item }}, {{ $optId }})"
                            wire:loading.attr="disabled"
                            wire:target="pickPlanItem({{ $day }}, {{ $item }}, {{ $optId }})"
                            @disabled($isCurrent)
                            @class([
                                'w-full flex items-center gap-3 rounded-lg border px-3 py-2 text-left transition-colors duration-150',
                                'border-madi-300 bg-white ring-1 ring-madi-200' => $isCurrent,
                                'border-moodhu-200 bg-white hover:border-madi-300 hover:bg-madi-50/80' => ! $isCurrent,
                                'opacity-70 cursor-default' => $isCurrent,
                            ])
                        >
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-muraka-900 line-clamp-2">{{ $optTitle }}</span>
                                @if($isCurrent)
                                    <span class="text-[10px] font-semibold text-madi-600">Current choice</span>
                                @endif
                            </span>
                            <x-ui.price :usd="$optUsd" :mvr="$optMvr" size="sm" />
                        </button>
                    </li>
                @endforeach
            </ul>
            <button
                type="button"
                wire:click.stop="closeChooser"
                class="text-xs font-medium text-muraka-500 hover:text-madi-700 transition-colors duration-150"
            >
                Cancel
            </button>
        </div>
    @endif
</div>
