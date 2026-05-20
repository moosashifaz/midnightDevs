<x-marketplace-layout>
    <div class="page-wrap">
    <div class="relative rounded-2xl overflow-hidden mb-6 h-36 sm:h-44">
        <img
            src="/images/categories/{{ $category }}.webp"
            alt="{{ $label }}"
            class="absolute inset-0 h-full w-full object-cover"
            loading="lazy"
        />
        <div class="absolute inset-0 bg-gradient-to-r from-muraka-900/70 to-muraka-900/30"></div>
        <div class="relative h-full flex flex-col justify-end p-5 text-white">
            <p class="text-xs font-semibold uppercase tracking-label text-moodhu-200">{{ $island?->name ?? 'All islands' }}</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $label }}</h1>
            <p class="text-sm text-moodhu-100 mt-0.5">{{ $listings->total() }} {{ Str::plural('listing', $listings->total()) }}</p>
        </div>
    </div>

    @if($listings->isEmpty())
        <x-ui.card class="p-10 text-center text-muraka-500">
            Nothing here yet. Try another category or island.
        </x-ui.card>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($listings as $listing)
                <x-ui.listing-card :listing="$listing">
                    <p class="text-xs text-muraka-600 line-clamp-2 mb-2">{{ $listing->description }}</p>
                </x-ui.listing-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $listings->links() }}
        </div>
    @endif
    </div>
</x-marketplace-layout>
