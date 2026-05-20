@props(['listing', 'class' => ''])

@php
$category = $listing->category ?? 'experience';
$gradients = [
    'eat' => 'from-iru-200 via-iru-100 to-dhooni-100',
    'wash' => 'from-moodhu-300 via-moodhu-100 to-miyaru-100',
    'buy' => 'from-dhooni-200 via-dhooni-100 to-iru-100',
    'experience' => 'from-miyaru-200 via-madi-100 to-madi-200',
];
$gradient = $gradients[$category] ?? $gradients['experience'];
$src = $listing->image_url ?? null;
@endphp

<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-moodhu-100 '.$class]) }}>
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $listing->title }}"
            loading="lazy"
            class="absolute inset-0 h-full w-full object-cover"
        />
    @else
        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br {{ $gradient }}">
            <x-icons.category :category="$category" class="w-12 h-12 opacity-60" />
        </div>
    @endif
</div>
