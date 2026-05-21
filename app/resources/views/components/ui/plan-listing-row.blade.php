@props([
    'listing' => [],
    'note' => null,
    'showBook' => false,
    'showAsk' => false,
    'editable' => false,
    'dayIndex' => 0,
    'itemIndex' => 0,
    'swapBarOpen' => false,
    'swapBarOptions' => [],
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
                    wire:click.stop="toggleSwapOptions({{ $day }}, {{ $item }})"
                    wire:loading.attr="disabled"
                    wire:target="toggleSwapOptions({{ $day }}, {{ $item }})"
                    @class([
                        'inline-flex items-center justify-center rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition-colors duration-150',
                        'border-madi-400 bg-madi-50 text-madi-800' => $swapBarOpen,
                        'border-moodhu-200 bg-white text-madi-700 hover:border-madi-300 hover:bg-madi-50' => ! $swapBarOpen,
                    ])
                    title="Pick another listing"
                >
                    <span wire:loading.remove wire:target="toggleSwapOptions({{ $day }}, {{ $item }})">
                        {{ $swapBarOpen ? 'Close' : 'Swap' }}
                    </span>
                    <span wire:loading wire:target="toggleSwapOptions({{ $day }}, {{ $item }})">…</span>
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
                        title="Learn more about this activity"
                        aria-label="Learn more about {{ $title }}"
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

    @if($editable && $swapBarOpen && count($swapBarOptions) > 0)
        <div class="plan-swap-bar" role="listbox" aria-label="Swap this activity">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="text-[10px] font-semibold uppercase tracking-label text-madi-700">Swap to</p>
                <button
                    type="button"
                    wire:click.stop="closeSwapBar"
                    class="text-[10px] font-medium text-muraka-500 hover:text-madi-700 transition-colors duration-150"
                >
                    Cancel
                </button>
            </div>
            <div class="plan-swap-bar-track flex gap-2 overflow-x-auto pb-1 snap-x snap-mandatory scroll-smooth">
                @foreach($swapBarOptions as $option)
                    @php
                        $opt = $option['listing'] ?? [];
                        $optId = (int) ($opt['id'] ?? 0);
                        $isCurrent = (bool) ($option['is_current'] ?? false);
                        $optTitle = $opt['title'] ?? 'Listing';
                        $optImage = $opt['image_url'] ?? null;
                        $optCategory = $opt['category'] ?? 'experience';
                        $optUsd = (float) ($opt['price_usd'] ?? 0);
                        $optMvr = (float) ($opt['price_mvr'] ?? 0);
                    @endphp
                    <button
                        type="button"
                        wire:key="swap-bar-{{ $day }}-{{ $item }}-{{ $optId }}"
                        wire:click.stop="pickSwapOption({{ $day }}, {{ $item }}, {{ $optId }})"
                        wire:loading.attr="disabled"
                        wire:target="pickSwapOption({{ $day }}, {{ $item }}, {{ $optId }})"
                        @disabled($isCurrent)
                        @class([
                            'plan-swap-bar-card snap-start shrink-0 flex flex-col rounded-xl border text-left transition-all duration-150',
                            'border-madi-400 bg-madi-50 ring-2 ring-madi-200' => $isCurrent,
                            'border-moodhu-200 bg-white hover:border-madi-300 hover:shadow-card' => ! $isCurrent,
                            'cursor-default' => $isCurrent,
                        ])
                    >
                        <div class="h-16 w-full overflow-hidden rounded-t-[10px] bg-moodhu-100">
                            @if($optImage)
                                <img src="{{ $optImage }}" alt="" class="h-full w-full object-cover" loading="lazy" />
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <x-icons.category :category="$optCategory" class="w-5 h-5 text-muraka-400" />
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col gap-1 p-2">
                            <span class="text-xs font-semibold text-muraka-900 line-clamp-2 leading-snug">{{ $optTitle }}</span>
                            @if($isCurrent)
                                <span class="text-[9px] font-bold uppercase tracking-wide text-madi-600">Selected</span>
                            @endif
                            <x-ui.price :usd="$optUsd" :mvr="$optMvr" size="sm" class="mt-auto" />
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
