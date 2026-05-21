@props([
    'size' => 200,
    'class' => '',
])

@php
    $pixels = max(24, (int) $size);
    $lottieSrc = asset('animations/AfterArrival-Logo-Animation.json');
@endphp

<div
    wire:ignore
    data-lottie-player
    data-lottie-src="{{ $lottieSrc }}"
    class="brand-lottie shrink-0 {{ $class }}"
    style="width: {{ $pixels }}px; height: {{ $pixels }}px;"
    aria-hidden="true"
    {{ $attributes->except(['size', 'class']) }}
></div>
