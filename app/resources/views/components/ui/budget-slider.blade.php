@props([
    'min' => 100,
    'max' => 10000,
    'step' => 50,
    'disabled' => false,
    'id' => 'budget-slider',
])

<div {{ $attributes->merge(['class' => 'rounded-xl bg-white/80 border border-moodhu-200 px-3 py-3 sm:px-4']) }}>
    <div class="flex items-center justify-between gap-3 mb-2">
        <label for="{{ $id }}" class="text-xs font-semibold uppercase tracking-label text-muraka-500">
            Fine-tune budget
        </label>
        <span class="text-sm font-bold text-madi-700 tabular-nums" x-text="'$' + Number(budget).toLocaleString()"></span>
    </div>
    <input
        id="{{ $id }}"
        type="range"
        class="planner-range w-full"
        min="{{ $min }}"
        max="{{ $max }}"
        step="{{ $step }}"
        x-model.number="budget"
        @disabled($disabled)
        aria-valuemin="{{ $min }}"
        aria-valuemax="{{ $max }}"
        :aria-valuenow="budget"
        aria-label="Trip budget in US dollars"
    />
    <div class="flex justify-between mt-1.5 text-[10px] text-muraka-400 tabular-nums">
        <span>${{ number_format($min, 0) }}</span>
        <span>${{ number_format($max, 0) }}</span>
    </div>
</div>
