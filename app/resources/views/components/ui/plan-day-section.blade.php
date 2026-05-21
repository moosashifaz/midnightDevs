@props([
    'day' => 1,
    'title' => '',
    'items' => [],
    'showBook' => false,
    'showAsk' => false,
    'editable' => false,
    'dayIndex' => 0,
    'swapBarOpen' => false,
    'swapBarItemIndex' => null,
    'swapBarOptions' => [],
    'compact' => false,
    'timeline' => false,
])

<div {{ $attributes->merge(['class' => $timeline ? 'planner-timeline-day' : '']) }}>
    @if($timeline)
        <div class="planner-timeline-marker" aria-hidden="true"></div>
    @endif

    <div class="{{ $timeline ? 'planner-timeline-content' : '' }}">
        <div class="mb-3">
            <p class="text-[10px] font-semibold uppercase tracking-label text-madi-600">Day {{ $day }}</p>
            <h3 class="font-semibold text-muraka-900">{{ $title }}</h3>
        </div>

        @if(empty($items))
            <p class="text-sm text-muraka-500">No items scheduled.</p>
        @else
            <div class="space-y-3 {{ $compact ? '' : 'rounded-2xl border border-moodhu-200 bg-moodhu-50/50 p-3' }}">
                @foreach($items as $itemIndex => $item)
                    <div wire:key="plan-item-{{ $dayIndex }}-{{ $itemIndex }}">
                        <x-ui.plan-listing-row
                            :listing="$item['listing'] ?? []"
                            :note="$item['note'] ?? null"
                            :show-book="$showBook"
                            :show-ask="$showAsk"
                            :editable="$editable"
                            :day-index="$dayIndex"
                            :item-index="$itemIndex"
                            :swap-bar-open="$swapBarOpen && $swapBarItemIndex === $itemIndex"
                            :swap-bar-options="$swapBarOpen && $swapBarItemIndex === $itemIndex ? $swapBarOptions : []"
                            :compact="$compact"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
