@props([
    'step',
    'title',
    'description',
    'accent' => 'madi',
    'accentPosition' => 'top-left',
])

@php
$accentClasses = match($accent) {
    'iru' => 'bg-iru-400',
    'ruh' => 'bg-ruh-500',
    'miyaru' => 'bg-miyaru-400',
    'dhooni' => 'bg-dhooni-400',
    default => 'bg-madi-500',
};
$accentPositionClasses = match($accentPosition) {
    'top-right' => 'top-0 right-0 w-24 h-24 rounded-bl-[2.5rem]',
    'bottom-right' => 'bottom-0 right-0 w-28 h-20 rounded-tl-[2.5rem]',
    'bottom-left' => 'bottom-0 left-0 w-28 h-20 rounded-tr-[2.5rem]',
    default => 'top-0 left-0 w-24 h-24 rounded-br-[2.5rem]',
};
@endphp

<article
    {{ $attributes->merge([
        'class' => 'value-card group relative flex flex-col overflow-hidden rounded-3xl border border-moodhu-200/80 bg-white p-6 pt-8 shadow-card transition-shadow duration-200 hover:shadow-card-hover focus-within:shadow-card-hover focus-within:ring-2 focus-within:ring-madi-300 focus-within:ring-offset-2 cursor-pointer',
    ]) }}
    role="button"
    tabindex="0"
>
    <div class="pointer-events-none absolute {{ $accentPositionClasses }} {{ $accentClasses }} opacity-90" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-x-0 top-16 h-px bg-moodhu-200" aria-hidden="true"></div>

    <span class="relative z-10 mb-3 text-4xl font-serif leading-none text-moodhu-300 select-none" aria-hidden="true">&ldquo;</span>

    <div class="relative z-10 flex items-center gap-2 mb-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-moodhu-100 text-xs font-bold text-madi-700">{{ $step }}</span>
        @if(isset($icon))
            <span class="text-madi-600">{{ $icon }}</span>
        @endif
    </div>

    <h3 class="relative z-10 text-base font-bold text-muraka-900 leading-snug mb-2">{{ $title }}</h3>
    <p class="relative z-10 text-sm text-muraka-600 leading-relaxed flex-1">{{ $description }}</p>

    <p class="relative z-10 mt-4 text-xs font-medium text-madi-600 opacity-0 transition-opacity duration-150 group-hover:opacity-100 group-focus-within:opacity-100">
        Tap to see how it works →
    </p>
</article>
