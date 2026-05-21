<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function home(Request $request): View
    {
        $island = $this->currentIsland($request);

        $featured = Listing::query()
            ->with(['provider', 'island'])
            ->when($island, fn ($q) => $q->where('island_id', $island->id))
            ->where('is_active', true)
            ->orderByDesc('rating')
            ->limit(6)
            ->get();

        $categories = collect(Listing::CATEGORIES)->map(function ($label, $key) use ($island) {
            $count = Listing::query()
                ->where('category', $key)
                ->where('is_active', true)
                ->when($island, fn ($q) => $q->where('island_id', $island->id))
                ->count();
            return [
                'key' => $key,
                'label' => $label,
                'sublabel' => Listing::CATEGORY_SUBLABELS[$key] ?? null,
                'count' => $count,
            ];
        })->values();

        return view('marketplace.home', [
            'island' => $island,
            'islands' => Island::orderBy('name')->get(),
            'featured' => $featured,
            'categories' => $categories,
        ]);
    }

    public function category(Request $request, string $category): View
    {
        if (! array_key_exists($category, Listing::CATEGORIES)) {
            abort(404);
        }

        $currentIsland = $this->currentIsland($request);
        $selectedIsland = $request->query('island');
        $selectedRating = $request->integer('rating') ?: null;
        $selectedLeadTime = $request->integer('lead_time') ?: null;
        $selectedSort = $request->query('sort');

        $listings = Listing::query()
            ->with(['provider', 'island'])
            ->where('category', $category)
            ->where('is_active', true)
            ->when($selectedIsland, fn ($q) => $q->whereHas('island', fn ($q) => $q->where('slug', $selectedIsland)))
            ->when($selectedRating, fn ($q) => $q->where('rating', '>=', $selectedRating))
            ->when($selectedLeadTime, fn ($q) => $q->where('lead_time_minutes', '<=', $selectedLeadTime))
            ->when($selectedSort === 'price_asc', fn ($q) => $q->orderBy('price_mvr', 'asc'))
            ->when($selectedSort === 'price_desc', fn ($q) => $q->orderBy('price_mvr', 'desc'))
            ->when($selectedSort === 'lead_time_asc', fn ($q) => $q->orderBy('lead_time_minutes', 'asc'))
            ->when($selectedSort === 'lead_time_desc', fn ($q) => $q->orderBy('lead_time_minutes', 'desc'))
            ->when($selectedSort === 'rating_asc', fn ($q) => $q->orderBy('rating', 'asc'))
            ->when($selectedSort === 'rating_desc' || !$selectedSort, fn ($q) => $q->orderByDesc('rating'))
            ->paginate(12)
            ->withQueryString();

        return view('marketplace.category', [
            'category' => $category,
            'label' => Listing::CATEGORIES[$category],
            'description' => Listing::CATEGORY_SUBLABELS[$category] ?? null,
            'island' => $currentIsland,
            'islands' => Island::orderBy('name')->get(),
            'listings' => $listings,
            'selectedIsland' => $selectedIsland,
            'selectedRating' => $selectedRating,
            'selectedLeadTime' => $selectedLeadTime,
            'selectedSort' => $selectedSort,
        ]);
    }

    public function listing(Listing $listing): View
    {
        $listing->load(['provider', 'island', 'reviews.user']);

        $related = Listing::where('category', $listing->category)
            ->where('island_id', $listing->island_id)
            ->where('id', '!=', $listing->id)
            ->where('is_active', true)
            ->limit(3)
            ->get();

        return view('marketplace.listing', [
            'listing' => $listing,
            'related' => $related,
        ]);
    }

    public function setIsland(Request $request, Island $island)
    {
        $request->session()->put('island_slug', $island->slug);

        if ($request->user()) {
            $request->user()->update(['current_island_id' => $island->id]);
        }

        return back();
    }

    protected function currentIsland(Request $request): ?Island
    {
        if ($request->user() && $request->user()->current_island_id) {
            return Island::find($request->user()->current_island_id);
        }

        if ($slug = $request->session()->get('island_slug')) {
            return Island::where('slug', $slug)->first();
        }

        return Island::where('is_pilot', true)->orderBy('id')->first();
    }
}
