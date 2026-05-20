<?php

namespace App\Services\Planner;

use App\Models\Listing;

/**
 * Structured itinerary for the AI island planner UI.
 *
 * @phpstan-type PlanListingSnapshot array{
 *     id: int,
 *     slug: string,
 *     title: string,
 *     category: string,
 *     price_usd: float,
 *     price_mvr: float,
 *     image_url: string|null,
 *     provider_name: string,
 * }
 * @phpstan-type PlanItemArray array{listing: PlanListingSnapshot, note: string|null}
 * @phpstan-type PlanDayArray array{day: int, title: string, items: list<PlanItemArray>}
 */
class PlanResult
{
    /**
     * @param  list<PlanDayArray>  $planDays
     */
    public function __construct(
        public float $budgetUsd,
        public int $dayCount,
        public float $spentUsd,
        public ?string $summary,
        public array $planDays,
        public bool $isSample = false,
    ) {
    }

    public static function listingSnapshot(Listing $listing): array
    {
        $listing->loadMissing('provider');

        return [
            'id' => $listing->id,
            'slug' => $listing->slug,
            'title' => $listing->title,
            'category' => $listing->category,
            'price_usd' => (float) $listing->price_usd,
            'price_mvr' => (float) $listing->price_mvr,
            'image_url' => $listing->image_url,
            'provider_name' => $listing->provider->business_name ?? 'Local provider',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'budget_usd' => $this->budgetUsd,
            'day_count' => $this->dayCount,
            'spent_usd' => $this->spentUsd,
            'summary' => $this->summary,
            'plan_days' => $this->planDays,
            'is_sample' => $this->isSample,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            budgetUsd: (float) ($data['budget_usd'] ?? 0),
            dayCount: (int) ($data['day_count'] ?? count($data['plan_days'] ?? [])),
            spentUsd: (float) ($data['spent_usd'] ?? 0),
            summary: $data['summary'] ?? null,
            planDays: $data['plan_days'] ?? [],
            isSample: (bool) ($data['is_sample'] ?? false),
        );
    }

    public function remainingUsd(): float
    {
        return max(0, $this->budgetUsd - $this->spentUsd);
    }

    public function spentPercent(): float
    {
        if ($this->budgetUsd <= 0) {
            return 0;
        }

        return min(100, ($this->spentUsd / $this->budgetUsd) * 100);
    }
}
