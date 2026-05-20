<x-marketplace-layout>
    <div class="page-wrap max-w-7xl mx-auto">
    <div class="mb-6 py-6">
        <div class="space-y-3">
            <div class="text-sm uppercase tracking-[0.24em] text-madi-700 font-semibold">{{ $listings->total() }} {{ Str::plural('listing', $listings->total()) }}</div>
            <div class="flex items-center gap-4">
            <div class="text-3xl sm:text-4xl font-bold text-muraka-900">{{ $label }}</div>
            @if($description)
                <p class="text-sm text-muraka-600 max-w-2xl border-l border-gray-200 pl-4 py-3">{{ $description }}</p>
            @endif
            </div>
        </div>
    </div>

    @if($listings->isEmpty())
        <x-ui.card class="p-10 text-center text-muraka-500">
            Nothing here yet. Try another category or island.
        </x-ui.card>
    @else
        <div class="grid sm:grid-cols-4 lg:grid-cols-4 gap-4">
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
