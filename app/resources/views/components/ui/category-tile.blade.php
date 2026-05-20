@props(['category', 'label', 'count' => null, 'href'])

<a href="{{ $href }}" class="card-interactive group flex flex-col overflow-hidden">
    <div class="aspect-[4/3] relative">
        <img
            src="/images/categories/{{ $category }}.webp"
            alt="{{ $label }}"
            loading="lazy"
            class="absolute inset-0 h-full w-full object-cover"
            onerror="this.style.display='none'"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-muraka-900/50 to-transparent"></div>
        <div class="absolute bottom-3 left-3 flex items-center gap-2 text-white">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
                <x-icons.category :category="$category" class="w-5 h-5 text-white" />
            </span>
            <span class="font-semibold">{{ $label }}</span>
        </div>
    </div>
    @if($count !== null)
        <div class="px-4 py-2 text-xs text-muraka-500">{{ $count }} {{ Str::plural('listing', $count) }}</div>
    @endif
</a>
