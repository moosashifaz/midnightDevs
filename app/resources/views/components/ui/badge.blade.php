@props(['variant' => 'default'])

@php
$class = match($variant) {
    'instant' => 'badge-instant',
    'lead' => 'badge-lead',
    'verified' => 'badge-verified',
    default => 'badge bg-moodhu-100 text-muraka-700',
};
@endphp

<span {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</span>
