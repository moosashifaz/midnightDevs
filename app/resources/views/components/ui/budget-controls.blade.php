@props([
    'min' => 100,
    'max' => 10000,
    'step' => 50,
    'presets' => [500, 2000, 5000],
    'disabled' => false,
])

@php
    $presets = is_array($presets) ? $presets : [500, 2000, 5000];
@endphp

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    <x-ui.budget-presets :presets="$presets" :disabled="$disabled" pick-action="budget = %d" />

    <x-ui.budget-slider
        :min="$min"
        :max="$max"
        :step="$step"
        :disabled="$disabled"
        id="plan-budget-slider"
    />
</div>
