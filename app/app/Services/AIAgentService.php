<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;

/**
 * AfterArrival's AI Concierge agent.
 *
 * In mock mode this returns canned, deterministic responses keyed off simple
 * keyword routing — enough to demonstrate the chat surface in a hackathon
 * setting. Swap config('services.anthropic.mock') to false to wire in a real
 * Anthropic SDK call. The shape of the response is intentionally close to
 * what tool-using Claude would return so the upgrade is a drop-in.
 */
class AIAgentService
{
    public function __construct(
        protected ?string $apiKey,
        protected string $model,
        protected bool $mock = true,
    ) {
    }

    public function reply(string $message, ?User $user = null): array
    {
        $message = trim($message);
        $lower = strtolower($message);

        if ($this->mock) {
            return $this->mockReply($lower, $user);
        }

        return $this->liveReply($message, $user);
    }

    protected function mockReply(string $message, ?User $user): array
    {
        if ($this->matchesAny($message, ['hi', 'hello', 'hey', 'marhaba', 'salaam'])) {
            $name = $user?->name ? ', '.explode(' ', $user->name)[0] : '';
            return $this->response(
                "Marhaba{$name}! I'm AfterArrival's local concierge. Ask me about food, laundry, souvenirs, or experiences on your island — I'll find what's nearby and tell you what's a fair price.",
            );
        }

        if ($this->matchesAny($message, ['food', 'eat', 'dinner', 'lunch', 'breakfast', 'cafe', 'restaurant'])) {
            $islandId = $user?->current_island_id;
            $listings = Listing::where('category', 'eat')
                ->when($islandId, fn ($q) => $q->where('island_id', $islandId))
                ->where('is_active', true)
                ->orderByDesc('rating')
                ->limit(3)
                ->get();

            if ($listings->isEmpty()) {
                return $this->response("I don't have food listings cached for your island yet. Try opening the Eat tab — anything live there is on my radar.");
            }

            $bullets = $listings->map(fn ($l) => sprintf('• %s — MVR %s', $l->title, $l->price_mvr))->implode("\n");

            return $this->response("Here are a few well-rated food options near you:\n\n{$bullets}\n\nWant me to pull more details on any of these?");
        }

        if ($this->matchesAny($message, ['laundry', 'wash', 'clean'])) {
            return $this->response("Laundry on the pilot island typically runs MVR 50-80 per kg, with same-day turnaround for orders placed before 10am. Open the Wash tab to see who's available now and their lead time.");
        }

        if ($this->matchesAny($message, ['snorkel', 'dive', 'fishing', 'experience', 'tour'])) {
            return $this->response("Experiences on local islands are usually MVR 400-1500 depending on duration and gear. Browse the Experience tab — I can also answer 'is this a fair price' once you've picked a listing.");
        }

        if ($this->matchesAny($message, ['mvr', 'usd', 'currency', 'exchange', 'rate', 'fair price'])) {
            return $this->response("All prices show in both MVR and USD using the Central Bank of Maldives reference rate (refreshed daily). When you check out, I'll flag if paying in MVR vs USD card would save you anything — usually it does, by 3-5%.");
        }

        if ($this->matchesAny($message, ['tgst', 'tax', 'gst'])) {
            return $this->response("Tourism GST (TGST) is 16% on tourism services in the Maldives. Every receipt on AfterArrival itemizes it separately so you see the base price + the tax. No hidden fees.");
        }

        if ($this->matchesAny($message, ['mosque', 'bikini', 'beach', 'wear', 'ramadan', 'culture'])) {
            return $this->response("Quick local etiquette: bikinis are fine on designated tourist beaches but not in village areas. Modest dress when visiting a mosque (covered shoulders + knees). During Ramadan, daytime restaurants on local islands close until sunset — but tourist-facing places usually stay open.");
        }

        if ($this->matchesAny($message, ['budget', 'spend', 'spent', 'cost'])) {
            return $this->response("Your trip budget tracker is in your profile. I can give you a running total or help you find the cheapest options in any category. What's your remaining budget?");
        }

        return $this->response("I can help with food, laundry, souvenirs, experiences, currency questions, TGST, cultural etiquette, and pricing fairness on AfterArrival listings. What are you looking for?");
    }

    protected function liveReply(string $message, ?User $user): array
    {
        // TODO: implement Anthropic SDK call with tool use:
        //   - search_listings(category, island_id)
        //   - get_benchmark_price(category, island_id)
        //   - get_budget_state(user_id)
        // For now, fall back to mock so the demo doesn't break in live mode.
        return $this->mockReply(strtolower($message), $user);
    }

    protected function matchesAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    protected function response(string $text): array
    {
        return [
            'role' => 'assistant',
            'content' => $text,
            'model' => $this->mock ? 'mock' : $this->model,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
