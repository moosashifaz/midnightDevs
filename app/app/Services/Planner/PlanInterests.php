<?php

namespace App\Services\Planner;

use App\Models\Listing;
use Illuminate\Support\Collection;

/**
 * Activity interests for trip planning (snorkeling, local food, etc.).
 *
 * @phpstan-type InterestOption array{label: string, hint: string, icon: string}
 */
class PlanInterests
{
    /** @var array<string, InterestOption> */
    public const OPTIONS = [
        'snorkeling' => [
            'label' => 'Snorkeling',
            'hint' => 'Reef trips, sea turtles, guided snorkel',
            'icon' => 'compass',
        ],
        'diving' => [
            'label' => 'Diving',
            'hint' => 'Scuba & dive excursions',
            'icon' => 'waves',
        ],
        'dolphins' => [
            'label' => 'Dolphin cruises',
            'hint' => 'Sunset dhoni & spinner pods',
            'icon' => 'waves',
        ],
        'sandbank' => [
            'label' => 'Sandbank trips',
            'hint' => 'Picnics on uninhabited sandbanks',
            'icon' => 'map-pin',
        ],
        'local-food' => [
            'label' => 'Local food',
            'hint' => 'Cafés, curry, home-cooked meals',
            'icon' => 'utensils',
        ],
        'laundry' => [
            'label' => 'Laundry',
            'hint' => 'Wash, dry, fold, express service',
            'icon' => 'shirt',
        ],
        'souvenirs' => [
            'label' => 'Souvenirs & crafts',
            'hint' => 'Handmade hats, lacquer, jewelry',
            'icon' => 'shopping-bag',
        ],
        'cooking' => [
            'label' => 'Cooking classes',
            'hint' => 'Hands-on Maldivian recipes',
            'icon' => 'utensils',
        ],
        'culture' => [
            'label' => 'Culture & tours',
            'hint' => 'Walking tours, island history',
            'icon' => 'map-pin',
        ],
        'language' => [
            'label' => 'Dhivehi language',
            'hint' => 'Useful phrases for your trip',
            'icon' => 'message-circle',
        ],
    ];

    public const ALL = [
        'snorkeling',
        'diving',
        'dolphins',
        'sandbank',
        'local-food',
        'laundry',
        'souvenirs',
        'cooking',
        'culture',
        'language',
    ];

    /** @param list<string> $included */
    public function __construct(public array $included)
    {
        $this->included = array_values(array_intersect(
            array_unique(array_map('strtolower', $included)),
            self::ALL,
        ));

        if ($this->included === []) {
            $this->included = self::ALL;
        }
    }

    public static function all(): self
    {
        return new self(self::ALL);
    }

    public static function fromQuery(?string $csv): self
    {
        if ($csv === null || trim($csv) === '') {
            return self::all();
        }

        $parts = array_filter(array_map('trim', explode(',', strtolower($csv))));

        return new self($parts);
    }

    /** @param list<string>|null $keys */
    public static function fromArray(?array $keys): self
    {
        if ($keys === null || $keys === []) {
            return self::all();
        }

        return new self($keys);
    }

    /** @return array<string, bool> */
    public static function defaultToggleState(): array
    {
        return array_fill_keys(self::ALL, true);
    }

    public function toQueryString(): string
    {
        return implode(',', $this->included);
    }

    public function contains(string $interest): bool
    {
        return in_array(strtolower($interest), $this->included, true);
    }

    public function isAll(): bool
    {
        return count($this->included) === count(self::ALL);
    }

    /** @return list<string> */
    public function labels(): array
    {
        return array_map(
            fn (string $key) => self::OPTIONS[$key]['label'] ?? ucfirst($key),
            $this->included,
        );
    }

    public function matchesListing(Listing $listing): bool
    {
        if ($this->isAll()) {
            return true;
        }

        $haystack = strtolower($listing->title.' '.$listing->description);

        foreach ($this->included as $interest) {
            if ($this->matchesInterest($interest, $listing, $haystack)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Collection<int, Listing>  $listings
     * @return Collection<int, Listing>
     */
    public function filterListings(Collection $listings): Collection
    {
        if ($this->isAll()) {
            return $listings;
        }

        return $listings->filter(fn (Listing $listing) => $this->matchesListing($listing))->values();
    }

    protected function matchesInterest(string $interest, Listing $listing, string $haystack): bool
    {
        return match ($interest) {
            'snorkeling' => $this->containsAny($haystack, ['snorkel', 'reef', 'turtle', 'underwater']),
            'diving' => $this->containsAny($haystack, ['dive', 'diving', 'scuba']),
            'dolphins' => $this->containsAny($haystack, ['dolphin', 'dhoni', 'cruise']),
            'sandbank' => $this->containsAny($haystack, ['sandbank', 'uninhabited', 'picnic']),
            'local-food' => $listing->category === 'eat'
                || $this->containsAny($haystack, ['cafe', 'curry', 'dinner', 'kitchen', 'meal', 'smoothie', 'lobster', 'huni', 'bajiya', 'hedhikaa', 'roshi']),
            'laundry' => $listing->category === 'wash'
                || $this->containsAny($haystack, ['laundry', 'wash', 'fold', 'ironing']),
            'souvenirs' => $listing->category === 'buy'
                || $this->containsAny($haystack, ['craft', 'souvenir', 'hat', 'bowl', 'earring', 'coconut-shell', 'lacquer', 'artisan']),
            'cooking' => $this->containsAny($haystack, ['cooking class', 'cook', 'recipe', 'chef']),
            'culture' => $this->containsAny($haystack, ['walking tour', 'island walk', 'mosque', 'village', 'history', 'cultural']),
            'language' => $this->containsAny($haystack, ['dhivehi', 'language', 'phrases', 'conversational']),
            default => false,
        };
    }

    /**
     * @param  list<string>  $needles
     */
    protected function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public function promptFocusLine(): string
    {
        $labels = $this->labels();

        if ($this->isAll()) {
            return 'Match the traveler\'s varied island interests across the trip (1-3 listings per day) using the listing catalog.';
        }

        $list = implode(', ', array_map(fn ($l) => "**{$l}**", $labels));

        return "Strongly prioritize listings that match these traveler interests: {$list}. Only use other listings if needed to fill the budget or days.";
    }

    public function promptExclusionLine(): string
    {
        if ($this->isAll()) {
            return '';
        }

        $excluded = array_values(array_diff(self::ALL, $this->included));
        $labels = array_map(
            fn (string $key) => self::OPTIONS[$key]['label'] ?? ucfirst($key),
            $excluded,
        );

        if ($labels === []) {
            return '';
        }

        return 'Deprioritize listings mainly about: '.implode(', ', $labels).'.';
    }
}
