@props(['eyebrow' => null, 'title', 'subtitle' => null])

<header {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if($eyebrow)
        <p class="section-eyebrow">{{ $eyebrow }}</p>
    @endif
    <h1 class="section-title mt-0.5">{{ $title }}</h1>
    @if($subtitle)
        <p class="text-sm text-muraka-500 mt-1">{{ $subtitle }}</p>
    @endif
</header>
