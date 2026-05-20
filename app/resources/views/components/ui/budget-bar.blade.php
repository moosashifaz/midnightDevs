@props([
    'spent' => 0,
    'total' => 0,
    'currency' => 'USD',
])

@php
    $spent = (float) $spent;
    $total = max(0.01, (float) $total);
    $remaining = max(0, $total - $spent);
    $percent = min(100, ($spent / $total) * 100);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <div class="flex items-center justify-between text-sm">
        <span class="text-muraka-600">
            <span class="font-semibold text-madi-700">{{ $currency }} {{ number_format($spent, 0) }}</span> used
        </span>
        <span class="text-muraka-500">
            {{ $currency }} {{ number_format($remaining, 0) }} left
        </span>
    </div>
    <div class="h-2 rounded-full bg-moodhu-200 overflow-hidden" role="progressbar" aria-valuenow="{{ round($percent) }}" aria-valuemin="0" aria-valuemax="100">
        <div
            class="h-full rounded-full bg-gradient-to-r from-madi-500 to-madi-400 transition-all duration-300"
            style="width: {{ $percent }}%"
        ></div>
    </div>
</div>
