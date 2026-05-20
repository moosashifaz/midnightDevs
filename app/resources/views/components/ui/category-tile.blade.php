@props(['category', 'label', 'sublabel' => null, 'count' => null, 'href'])

<a href="{{ $href }}" class="card-interactive group flex flex-col overflow-hidden">
    <div class="aspect-[4/3] relative bg-moodhu-100">
        <img
            src="/images/categories/{{ $category }}.webp"
            alt="{{ $label }}"
            loading="lazy"
            class="absolute inset-0 h-full w-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-muraka-900/60 via-muraka-900/10 to-transparent"></div>
        <div class="absolute bottom-3 left-3 flex items-start gap-3 text-white">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <x-icons.category :category="$category" class="w-5 h-5 text-white" />
            </span>
            <div>
                <span class="block font-semibold">{{ $label }}</span>
                @if($sublabel)
                    <span class="block text-xs opacity-90 leading-snug">{{ $sublabel }}</span>
                @endif
            </div>
        </div>
    </div>
    @if($count !== null)
        <div class="px-4 py-2 text-xs text-muraka-500">{{ $count }} {{ Str::plural('listing', $count) }}</div>
    @endif
</a>
