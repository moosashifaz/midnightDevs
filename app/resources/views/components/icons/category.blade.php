@props(['category', 'class' => 'w-6 h-6'])

@php
$map = [
    'eat' => 'utensils',
    'wash' => 'shirt',
    'buy' => 'shopping-bag',
    'experience' => 'compass',
];
$defaultColor = match($category) {
    'eat' => 'text-iru-600',
    'wash' => 'text-moodhu-700',
    'buy' => 'text-dhooni-700',
    'experience' => 'text-madi-600',
    default => 'text-muraka-600',
};
$iconClass = str_contains($class, 'text-') ? $class : trim($class.' '.$defaultColor);
@endphp

<x-icons.icon :name="$map[$category] ?? 'compass'" class="{{ $iconClass }}" />
