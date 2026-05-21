<?php

namespace App\Services\Planner;

use App\Models\Island;
use App\Models\Listing;
use Illuminate\Support\Collection;

/**
 * Finds swap candidates for a plan slot from live island listings.
 */
class PlanItemAlternatives
{
    /**
     * @param  list<array<string, mixed>>  $planDays
     * @return Collection<int, Listing>
     */
    public function forSlot(
        Island $island,
        array $planDays,
        int $dayIndex,
        int $itemIndex,
        float $budgetUsd,
    ): Collection {
        $item = $planDays[$dayIndex]['items'][$itemIndex] ?? null;
        if (! $item) {
            return collect();
        }

        $category = $item['listing']['category'] ?? null;
        if (! $category) {
            return collect();
        }

        $usedIds = $this->usedListingIds($planDays, $dayIndex, $itemIndex);
        $spentWithout = $this->spentUsd($planDays)
            - (float) ($item['listing']['price_usd'] ?? 0);

        return Listing::query()
            ->with('provider')
            ->where('island_id', $island->id)
            ->where('is_active', true)
            ->where('category', $category)
            ->orderByDesc('rating')
            ->orderBy('id')
            ->get()
            ->filter(function (Listing $listing) use ($usedIds, $spentWithout, $budgetUsd) {
                if ($usedIds->contains($listing->id)) {
                    return false;
                }

                return $spentWithout + (float) $listing->price_usd <= $budgetUsd;
            })->values();
    }

    /**
     * @param  list<array<string, mixed>>  $planDays
     * @return list<array{listing: array<string, mixed>, is_current: bool}>
     */
    public function optionsForSlot(
        Island $island,
        array $planDays,
        int $dayIndex,
        int $itemIndex,
        float $budgetUsd,
    ): array {
        $item = $planDays[$dayIndex]['items'][$itemIndex] ?? null;
        if (! $item) {
            return [];
        }

        $currentId = (int) ($item['listing']['id'] ?? 0);
        $candidates = $this->forSlot($island, $planDays, $dayIndex, $itemIndex, $budgetUsd);

        $current = $currentId
            ? Listing::query()->with('provider')->find($currentId)
            : null;

        if ($current && ! $candidates->contains(fn (Listing $l) => $l->id === $current->id)) {
            $candidates = collect([$current])->merge($candidates)->unique('id')->values();
        }

        return $candidates->map(fn (Listing $listing) => [
            'listing' => PlanResult::listingSnapshot($listing),
            'is_current' => $listing->id === $currentId,
        ])->all();
    }

    /**
     * @param  Collection<int, Listing>  $candidates
     */
    public function nextSwap(Listing $current, Collection $candidates): ?Listing
    {
        $pool = $candidates->unique('id')->values();

        $others = $pool->filter(fn (Listing $l) => $l->id !== $current->id)->values();

        if ($others->isNotEmpty()) {
            return $others->first();
        }

        if ($pool->count() <= 1) {
            return null;
        }

        $index = $pool->search(fn (Listing $l) => $l->id === $current->id);

        if ($index === false) {
            return $pool->first();
        }

        return $pool[($index + 1) % $pool->count()];
    }

    /**
     * @param  list<array<string, mixed>>  $planDays
     */
    public function spentUsd(array $planDays): float
    {
        $spent = 0.0;

        foreach ($planDays as $dayBlock) {
            foreach ($dayBlock['items'] ?? [] as $item) {
                $spent += (float) ($item['listing']['price_usd'] ?? 0);
            }
        }

        return round($spent, 2);
    }

    /**
     * @param  list<array<string, mixed>>  $planDays
     * @return Collection<int, int>
     */
    protected function usedListingIds(array $planDays, int $excludeDayIndex, int $excludeItemIndex): Collection
    {
        $ids = collect();

        foreach ($planDays as $dayIndex => $dayBlock) {
            foreach ($dayBlock['items'] ?? [] as $itemIndex => $item) {
                if ($dayIndex === $excludeDayIndex && $itemIndex === $excludeItemIndex) {
                    continue;
                }

                $id = $item['listing']['id'] ?? null;
                if ($id) {
                    $ids->push((int) $id);
                }
            }
        }

        return $ids;
    }
}
