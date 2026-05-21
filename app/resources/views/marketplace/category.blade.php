<x-marketplace-layout>
    <div class="page-wrap max-w-7xl mx-auto">
    <div class="mb-6 py-6">
        <div class="space-y-3">
            <div class="text-sm uppercase tracking-[0.24em] text-madi-700 font-semibold">{{ $listings->total() }} {{ Str::plural('listing', $listings->total()) }}</div>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="text-3xl sm:text-4xl font-bold text-muraka-900">{{ $label }}</div>
                        @if($description)
                            <p class="text-sm text-muraka-600 max-w-2xl mt-2">{{ $description }}</p>
                        @endif
                    </div>
                    <form method="GET" action="{{ route('category', $category) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 xl:grid-cols-[220px_160px_160px_160px_auto] items-end">
                        <label class="sr-only" for="island">Location</label>
                        <select id="island" name="island" class="input-field w-full py-2 px-3 text-sm" aria-label="Filter by location">
                            <option value="">All locations</option>
                            @foreach($islands as $filterIsland)
                                <option value="{{ $filterIsland->slug }}" {{ $selectedIsland === $filterIsland->slug ? 'selected' : '' }}>{{ $filterIsland->name }}</option>
                            @endforeach
                        </select>

                        <label class="sr-only" for="rating">Reviews</label>
                        <select id="rating" name="rating" class="input-field w-full py-2 px-3 text-sm" aria-label="Filter by rating">
                            <option value="">All reviews</option>
                            <option value="5" {{ $selectedRating === 5 ? 'selected' : '' }}>5 stars</option>
                            <option value="4" {{ $selectedRating === 4 ? 'selected' : '' }}>4+ stars</option>
                            <option value="3" {{ $selectedRating === 3 ? 'selected' : '' }}>3+ stars</option>
                            <option value="2" {{ $selectedRating === 2 ? 'selected' : '' }}>2+ stars</option>
                        </select>

                        <label class="sr-only" for="lead_time">Lead time</label>
                        <select id="lead_time" name="lead_time" class="input-field w-full py-2 px-3 text-sm" aria-label="Filter by lead time">
                            <option value="">Any lead time</option>
                            <option value="15" {{ $selectedLeadTime === 15 ? 'selected' : '' }}>15 min or less</option>
                            <option value="30" {{ $selectedLeadTime === 30 ? 'selected' : '' }}>30 min or less</option>
                            <option value="60" {{ $selectedLeadTime === 60 ? 'selected' : '' }}>60 min or less</option>
                        </select>

                        <label class="sr-only" for="sort">Price / rating</label>
                        <select id="sort" name="sort" class="input-field w-full py-2 px-3 text-sm" aria-label="Sort listings">
                            <option value="rating_desc" {{ $selectedSort === 'rating_desc' || !$selectedSort ? 'selected' : '' }}>Best rated</option>
                            <option value="rating_asc" {{ $selectedSort === 'rating_asc' ? 'selected' : '' }}>Lowest rated</option>
                            <option value="price_asc" {{ $selectedSort === 'price_asc' ? 'selected' : '' }}>Price low-high</option>
                            <option value="price_desc" {{ $selectedSort === 'price_desc' ? 'selected' : '' }}>Price high-low</option>
                            <option value="lead_time_asc" {{ $selectedSort === 'lead_time_asc' ? 'selected' : '' }}>Fastest</option>
                            <option value="lead_time_desc" {{ $selectedSort === 'lead_time_desc' ? 'selected' : '' }}>Longest wait</option>
                        </select>

                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-primary w-full py-2 text-sm">Apply</button>
                            @if($selectedIsland || $selectedRating || $selectedLeadTime || $selectedSort)
                                <a href="{{ route('category', $category) }}" class="text-sm text-madi-600 hover:text-madi-700 font-medium transition-colors">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>
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
