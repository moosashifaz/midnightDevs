<?php

namespace App\Services;

use App\Models\Island;
use App\Models\Listing;
use App\Models\User;
use App\Services\Anthropic\Client;
use App\Services\Planner\PlanResult;
use Illuminate\Support\Facades\Log;

class AIPlannerService
{
    public function __construct(
        protected SamplePlanService $samplePlans,
        protected Client $client,
    ) {
    }

    public function generate(?Island $island, float $budgetUsd, int $days, ?User $user = null): PlanResult
    {
        $days = max(1, min(7, $days));
        $budgetUsd = max(100, $budgetUsd);

        if (! $island) {
            return $this->samplePlans->forIsland(null, $budgetUsd, $days);
        }

        if (! $this->client->isAvailable()) {
            return $this->samplePlans->forIsland($island, $budgetUsd, $days);
        }

        try {
            $parsed = $this->requestPlanJson($island, $budgetUsd, $days, $user);
            if ($parsed) {
                return $this->hydratePlan($island, $budgetUsd, $days, $parsed);
            }
        } catch (\Throwable $e) {
            Log::error('ai.planner.exception', [
                'message' => $e->getMessage(),
                'island' => $island->slug,
            ]);
        }

        return $this->samplePlans->forIsland($island, $budgetUsd, $days);
    }

    /**
     * @return array{days: array<int, array<string, mixed>>, summary?: string}|null
     */
    protected function requestPlanJson(Island $island, float $budgetUsd, int $days, ?User $user): ?array
    {
        $listings = Listing::with('provider')
            ->where('island_id', $island->id)
            ->where('is_active', true)
            ->orderBy('category')
            ->orderByDesc('rating')
            ->get();

        if ($listings->isEmpty()) {
            return null;
        }

        $listingLines = $listings->map(fn (Listing $l) => sprintf(
            '- slug: %s | %s | %s | USD %.2f | MVR %s',
            $l->slug,
            $l->title,
            $l->categoryLabel(),
            (float) $l->price_usd,
            number_format((float) $l->price_mvr, 0),
        ))->implode("\n");

        $userLine = $user?->name
            ? 'Traveler: '.explode(' ', $user->name)[0]
            : 'Traveler: guest';

        $system = <<<PROMPT
You are AfterArrival's island trip planner. Build a multi-day itinerary using ONLY listings provided below.

Rules:
- Output ONLY valid JSON (no markdown fences, no commentary).
- Schema: {"days":[{"day":1,"title":"short label","items":[{"slug":"listing-slug","note":"optional time/activity"}]}],"summary":"one sentence"}
- Use only slugs from the listing list. Never invent slugs.
- Total USD of all items must not exceed {$budgetUsd}.
- Exactly {$days} day objects, day numbers 1..{$days}.
- Mix **Taste** (food), **Refresh** (laundry/wellness), **Shop** (pickup souvenirs), and **Explore** (experiences) across the trip (1-3 items per day).
- Notes are short (e.g. "Dinner", "Morning snorkel").
PROMPT;

        $userMessage = <<<MSG
Island: {$island->name}
Budget: USD {$budgetUsd} for {$days} days
{$userLine}

Listings:
{$listingLines}

Return the JSON plan now.
MSG;

        $text = $this->client->messages(
            $system,
            [['role' => 'user', 'content' => $userMessage]],
            maxTokens: 2048,
            timeoutSeconds: 90,
        );

        if ($text === null) {
            return null;
        }

        return $this->parseJsonPayload($text);
    }

    /**
     * @param  array{days: array<int, array<string, mixed>>, summary?: string}  $payload
     */
    protected function hydratePlan(Island $island, float $budgetUsd, int $days, array $payload): PlanResult
    {
        $bySlug = Listing::with('provider')
            ->where('island_id', $island->id)
            ->where('is_active', true)
            ->get()
            ->keyBy('slug');

        $spent = 0.0;
        $planDays = [];

        foreach ($payload['days'] ?? [] as $dayBlock) {
            $items = [];
            foreach ($dayBlock['items'] ?? [] as $item) {
                $slug = $item['slug'] ?? null;
                if (! $slug || ! $bySlug->has($slug)) {
                    continue;
                }
                $listing = $bySlug->get($slug);
                $price = (float) $listing->price_usd;
                if ($spent + $price > $budgetUsd) {
                    continue;
                }
                $spent += $price;
                $items[] = [
                    'listing' => PlanResult::listingSnapshot($listing),
                    'note' => isset($item['note']) ? (string) $item['note'] : null,
                ];
            }

            if (! empty($items)) {
                $planDays[] = [
                    'day' => (int) ($dayBlock['day'] ?? count($planDays) + 1),
                    'title' => (string) ($dayBlock['title'] ?? 'Island day'),
                    'items' => $items,
                ];
            }
        }

        if (empty($planDays)) {
            return $this->samplePlans->forIsland($island, $budgetUsd, $days);
        }

        return new PlanResult(
            budgetUsd: $budgetUsd,
            dayCount: $days,
            spentUsd: round($spent, 2),
            summary: $payload['summary'] ?? "Your {$days}-day plan on {$island->name}.",
            planDays: $planDays,
            isSample: false,
        );
    }

    /**
     * @return array{days: array<int, array<string, mixed>>, summary?: string}|null
     */
    protected function parseJsonPayload(string $text): ?array
    {
        $text = trim($text);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $text, $m)) {
            $text = trim($m[1]);
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        $decoded = json_decode(substr($text, $start, $end - $start + 1), true);

        return is_array($decoded) && isset($decoded['days']) ? $decoded : null;
    }
}
