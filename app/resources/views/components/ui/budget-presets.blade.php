@props([
    'presets' => [500, 2000, 5000],
    'disabled' => false,
    'pickAction' => 'budget = %d',
])

@php
    $presets = is_array($presets) ? $presets : [500, 2000, 5000];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
    <span class="text-[10px] uppercase tracking-label text-muraka-500 w-full sm:w-auto sm:mr-1 self-center">Budget</span>
    @foreach($presets as $amount)
        <button
            type="button"
            @click="{{ sprintf(str_replace('%d', (string) (int) $amount, $pickAction)) }}"
            :class="budget === {{ (int) $amount }} ? 'planner-chip-active' : 'planner-chip'"
            @disabled($disabled)
        >
            ${{ number_format($amount, 0) }}
        </button>
    @endforeach
</div>
