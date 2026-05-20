<?php

namespace App\Services;

use App\Models\Island;
use App\Models\Listing;
use App\Services\Planner\PlanResult;

class SamplePlanService
{
    private const DAY_TITLES = [
        1 => 'Settle in & local flavors',
        2 => 'Culture & island life',
        3 => 'Reef & open water',
        4 => 'Souvenirs & slow morning',
        5 => 'Farewell feast & memories',
        6 => 'Extra island day',
        7 => 'Final adventures',
    ];

    private const ITEM_NOTES = [
        'eat' => 'Meal',
        'wash' => 'Laundry',
        'buy' => 'Pickup',
        'experience' => 'Experience',
    ];

    public function forIsland(?Island $island, float $budgetUsd = 2000, int $days = 5): PlanResult
    {
        $days = max(1, min(7, $days));
        $budgetUsd = max(100, $budgetUsd);

        if (! $island) {
            return $this->emptyPlan($budgetUsd, $days);
        }

        $pool = Listing::query()
            ->with('provider')
            ->where('island_id', $island->id)
            ->where('is_active', true)
            ->orderByDesc('rating')
            ->get();

        if ($pool->isEmpty()) {
            return $this->emptyPlan($budgetUsd, $days);
        }

        $targetSpend = $budgetUsd * 0.72;
        $byCategory = $pool->groupBy('category');
        $usedIds = [];
        $spent = 0.0;
        $planDays = [];

        for ($d = 1; $d <= $days; $d++) {
            $items = [];
            $categoriesThisDay = $this->categoriesForDay($d, $days);

            foreach ($categoriesThisDay as $category) {
                if ($spent >= $targetSpend) {
                    break;
                }

                $candidate = $byCategory->get($category, collect())
                    ->first(fn (Listing $l) => ! in_array($l->id, $usedIds, true)
                        && ($spent + (float) $l->price_usd) <= $budgetUsd);

                if (! $candidate) {
                    $candidate = $pool->first(fn (Listing $l) => ! in_array($l->id, $usedIds, true)
                        && ($spent + (float) $l->price_usd) <= $budgetUsd);
                }

                if (! $candidate) {
                    continue;
                }

                $usedIds[] = $candidate->id;
                $spent += (float) $candidate->price_usd;
                $items[] = [
                    'listing' => PlanResult::listingSnapshot($candidate),
                    'note' => self::ITEM_NOTES[$category] ?? null,
                ];
            }

            if (empty($items) && $d === 1) {
                $fallback = $pool->first();
                if ($fallback) {
                    $usedIds[] = $fallback->id;
                    $spent += (float) $fallback->price_usd;
                    $items[] = [
                        'listing' => PlanResult::listingSnapshot($fallback),
                        'note' => null,
                    ];
                }
            }

            $planDays[] = [
                'day' => $d,
                'title' => self::DAY_TITLES[$d] ?? "Day {$d}",
                'items' => $items,
            ];
        }

        $islandName = $island->name;

        return new PlanResult(
            budgetUsd: $budgetUsd,
            dayCount: $days,
            spentUsd: round($spent, 2),
            summary: "A {$days}-day sample on {$islandName} — food, laundry, souvenirs, and experiences from verified local providers.",
            planDays: $planDays,
            isSample: true,
        );
    }

    /**
     * @return list<string>
     */
    protected function categoriesForDay(int $day, int $totalDays): array
    {
        $rotation = ['experience', 'eat', 'wash', 'buy', 'eat', 'experience', 'buy'];
        $categories = [];

        if ($day === 1) {
            $categories = ['eat', 'wash'];
        } elseif ($day === $totalDays) {
            $categories = ['eat', 'experience'];
        } else {
            $categories = [$rotation[($day - 1) % count($rotation)]];
            if ($day % 2 === 0) {
                $categories[] = 'eat';
            }
        }

        return array_values(array_unique($categories));
    }

    protected function emptyPlan(float $budgetUsd, int $days): PlanResult
    {
        return new PlanResult(
            budgetUsd: $budgetUsd,
            dayCount: $days,
            spentUsd: 0,
            summary: 'No listings on this island yet — browse categories when providers join.',
            planDays: [],
            isSample: true,
        );
    }
}
