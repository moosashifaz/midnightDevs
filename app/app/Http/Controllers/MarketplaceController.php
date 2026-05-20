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

        $island = $this->currentIsland($request);

        $listings = Listing::query()
            ->with(['provider', 'island'])
            ->where('category', $category)
            ->where('is_active', true)
            ->when($island, fn ($q) => $q->where('island_id', $island->id))
            ->orderByDesc('rating')
            ->paginate(12);

        return view('marketplace.category', [
            'category' => $category,
            'label' => Listing::CATEGORIES[$category],
            'island' => $island,
            'listings' => $listings,
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
