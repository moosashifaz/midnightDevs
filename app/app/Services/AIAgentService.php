<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use App\Services\Anthropic\Client;
use Illuminate\Support\Facades\Log;

/**
 * AfterArrival's AI Concierge — Anthropic Messages API.
 */
class AIAgentService
{
    public const HISTORY_CAP = 20;

    public function __construct(
        protected Client $client,
    ) {
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function reply(string $message, ?User $user = null, array $history = []): array
    {
        $message = trim($message);

        if (! $this->client->isAvailable()) {
            return $this->mockReply(strtolower($message), $user);
        }

        try {
            return $this->liveReply($message, $user, $history);
        } catch (\Throwable $e) {
            Log::error('anthropic.api.exception', [
                'message' => $e->getMessage(),
                'class' => $e::class,
            ]);

            return $this->response(
                "I had trouble connecting just now. Try again in a moment — or browse the categories above for what you need."
            );
        }
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function liveReply(string $message, ?User $user, array $history): array
    {
        $system = $this->buildSystemPrompt($user);
        $messages = $this->prepareMessages($history, $message);

        $text = $this->client->messages($system, $messages, maxTokens: 1024);

        if ($text === null) {
            return $this->response(
                "I'm having trouble reaching the concierge brain. Try again in a moment, or browse the categories directly."
            );
        }

        return [
            'role' => 'assistant',
            'content' => $text,
            'model' => $this->client->model(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    protected function buildSystemPrompt(?User $user): string
    {
        $userBlock = '';
        $listingsBlock = '';

        if ($user) {
            $island = $user->currentIsland;
            $name = explode(' ', $user->name)[0] ?? null;

            $userLines = ['## About the user'];
            if ($name) {
                $userLines[] = "- First name: {$name}";
            }
            if ($island) {
                $atoll = $island->atoll ? " ({$island->atoll} Atoll)" : '';
                $userLines[] = "- Currently staying on: {$island->name}{$atoll}";
            }
            if ($user->trip_start) {
                $end = $user->trip_end ? $user->trip_end->format('M j') : 'unknown';
                $userLines[] = "- Trip dates: {$user->trip_start->format('M j')} → {$end}";
                $remaining = now()->diffInDays($user->trip_end ?? now(), false);
                if ($remaining > 0) {
                    $userLines[] = "- Days left on island: {$remaining}";
                }
            }
            $userLines[] = "- Currency preference: {$user->currency_preference}";
            $userBlock = "\n\n".implode("\n", $userLines);

            if ($island) {
                $listings = Listing::with('provider')
                    ->where('island_id', $island->id)
                    ->where('is_active', true)
                    ->orderBy('category')
                    ->orderByDesc('rating')
                    ->get();

                if ($listings->isNotEmpty()) {
                    $lines = ["\n## Live listings on {$island->name} right now",
                        '(use ONLY these when recommending — do not invent others)'];

                    foreach ($listings->groupBy('category') as $cat => $items) {
                        $catLabel = Listing::CATEGORIES[$cat] ?? ucfirst((string) $cat);
                        $lines[] = "\n### {$catLabel}";
                        foreach ($items as $listing) {
                            $lines[] = sprintf(
                                '- **%s** by %s — USD %s (≈ MVR %s) · ★ %s · %s',
                                $listing->title,
                                $listing->provider->business_name ?? 'Unknown provider',
                                number_format((float) $listing->price_usd, 2),
                                number_format((float) $listing->price_mvr, 0),
                                number_format((float) $listing->rating, 1),
                                $listing->lead_time_minutes > 0
                                    ? "{$listing->lead_time_minutes}min lead time"
                                    : 'instant',
                            );
                        }
                    }

                    $listingsBlock = "\n".implode("\n", $lines);
                }
            }
        }

        return <<<PROMPT
You are AfterArrival's local concierge — a warm, knowledgeable AI guide inside a Maldives in-stay services web app. Tourists open the app *after they arrive* on a local Maldivian island and use you to find food, laundry, souvenirs, and cultural experiences nearby. You're the kind of local friend a first-time visitor wishes they had.

## About AfterArrival
- A marketplace for things tourists need *during* their stay on inhabited Maldivian islands.
- Payments via **BML Swipe** (the dominant Maldivian payment rail).
- 16% Tourism GST (TGST) applies and is itemized on every receipt.
- Categories on the platform: **Taste**, **Refresh**, **Shop** (pickup only — no delivery), **Explore**.
- Out of scope by design: accommodation/room booking, airport transfers, inter-island boats, resort-internal services.
{$userBlock}{$listingsBlock}

## How you respond
- Warm, helpful, **concise** by default. 1–3 short paragraphs unless detailed advice is genuinely needed.
- Use Markdown sparingly — bullet points and **bold** for prices and names work well.
- Always cite prices **USD first**, then MVR in parentheses (1 USD ≈ 15.4 MVR). Tourists think in dollars.
- Be opinionated when asked for a recommendation. Say what *you'd* do, not just options.
- When discussing a price, say whether it's typical / above / below the local Maldives range, and give the rough range.
- Use the user's first name occasionally when it fits naturally — never in every sentence.

## Hard rules — never violate
- **Never invent listings, providers, prices, or contact details.** If the user wants something that isn't in the live listings above, say so plainly and suggest they browse the relevant category — **Taste**, **Refresh**, **Shop**, or **Explore**.
- **Refuse politely** for: alcohol, recreational drugs, adult services, nightlife, pork. These are illegal or unavailable on inhabited Maldivian islands and are not part of the platform.
- **Defer to authority** for medical, legal, or emergency situations. Tell the user to contact: local clinic, police (119 in Maldives), their embassy, or a real professional. You are not a substitute.
- **Stay in scope.** If asked about accommodation, transfers, or transport, briefly explain those aren't on AfterArrival and suggest Booking.com / Atoll Transfer / their guesthouse — then redirect to what you can help with.
- **Don't make up Maldivian facts.** If unsure, say so.

## Cultural context to weave in when relevant
- Bikinis are fine on designated tourist beaches; modest dress (shoulders + knees covered) in village areas and when visiting a mosque.
- During Ramadan, daytime restaurants on local islands often close until iftar — tourist-focused places usually stay open.
- Tipping isn't expected but is appreciated — 10% for excursion crew is generous.
- "Marhaba" (welcome) and "Shukuriyaa" (thank you) go a long way with locals.
- Maldivian Rufiyaa (MVR) is the local currency; USD is widely accepted at tourist-facing shops, often at slightly worse rates.

Now reply to the user.
PROMPT;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array<int, array{role: string, content: string}>
     */
    protected function prepareMessages(array $history, string $latestUserMessage): array
    {
        $capped = array_slice($history, -self::HISTORY_CAP);

        $messages = [];
        $expected = 'user';
        foreach ($capped as $msg) {
            if (! isset($msg['role'], $msg['content'])) {
                continue;
            }
            if ($msg['role'] !== $expected) {
                continue;
            }
            $messages[] = ['role' => $msg['role'], 'content' => (string) $msg['content']];
            $expected = $expected === 'user' ? 'assistant' : 'user';
        }

        if (! empty($messages) && $messages[0]['role'] !== 'user') {
            array_shift($messages);
        }

        $last = end($messages);
        if ($last && $last['role'] === 'user') {
            $messages[count($messages) - 1] = ['role' => 'user', 'content' => $latestUserMessage];
        } else {
            $messages[] = ['role' => 'user', 'content' => $latestUserMessage];
        }

        return $messages;
    }

    protected function mockReply(string $message, ?User $user): array
    {
        if ($this->matchesAny($message, ['hi', 'hello', 'hey', 'marhaba', 'salaam'])) {
            $name = $user?->name ? ', '.explode(' ', $user->name)[0] : '';

            return $this->response(
                "Marhaba{$name}! I'm AfterArrival's local concierge. Ask me about **Taste**, **Refresh**, **Shop**, or **Explore** on your island — I'll find what's nearby and tell you what's a fair price.",
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
                return $this->response("I don't have food listings cached for your island yet. Try opening the **Taste** tab.");
            }

            $bullets = $listings->map(fn ($l) => sprintf('• %s — $%s (≈ MVR %s)', $l->title, number_format((float) $l->price_usd, 2), number_format((float) $l->price_mvr, 0)))->implode("\n");

            return $this->response("Here are a few well-rated food options:\n\n{$bullets}\n\nWant more details on any of these?");
        }

        return $this->response("I can help with **Taste** (food), **Refresh** (laundry & wellness), **Shop** (pickup souvenirs), **Explore** (experiences), plus currency, TGST, cultural etiquette, and fair pricing. What are you looking for?");
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
            'model' => $this->client->isAvailable() ? $this->client->model() : 'mock',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
