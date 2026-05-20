@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
$class = match($variant) {
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'ghost' => 'btn-ghost',
    default => 'btn-primary',
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif
