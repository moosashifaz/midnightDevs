@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-madi-500 text-start text-base font-medium text-madi-700 bg-moodhu-100'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-muraka-600 hover:text-muraka-900 hover:bg-moodhu-50 hover:border-moodhu-300 transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
