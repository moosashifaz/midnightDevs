@props([
    'usd',
    'mvr' => null,
    'size' => 'md',
    'stacked' => true,
])

@php
    $usd = (float) $usd;
    $mvr = $mvr !== null ? (float) $mvr : null;
    $primaryClass = match ($size) {
        'lg' => 'text-3xl font-bold text-madi-700',
        'sm' => 'text-sm font-bold text-madi-700',
        default => 'text-lg font-bold text-madi-700',
    };
    $secondaryClass = match ($size) {
        'lg' => 'text-sm text-muraka-500',
        default => 'text-[11px] text-muraka-500',
    };
@endphp

<div {{ $attributes->merge(['class' => $stacked ? '' : 'inline-flex items-baseline gap-2 flex-wrap']) }}>
    <span class="{{ $primaryClass }}">${{ number_format($usd, $size === 'lg' ? 2 : ($usd >= 100 ? 0 : 2)) }}</span>
    @if($mvr !== null)
        <span class="{{ $secondaryClass }} {{ $stacked ? 'block' : '' }}">≈ MVR {{ number_format($mvr, 0) }}</span>
    @endif
</div>
