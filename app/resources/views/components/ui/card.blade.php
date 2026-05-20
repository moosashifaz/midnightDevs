@props(['href' => null, 'interactive' => false])

@php
$class = $interactive || $href ? 'card-interactive block' : 'card';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <div {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</div>
@endif
