<?php

namespace App\Services\Planner;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Builds Google Calendar "template" URLs for demo export (no API / OAuth).
 */
class PlanGoogleCalendarUrl
{
    private const string TIMEZONE = 'Indian/Maldives';

    /** @var list<int> */
    private const array SLOT_HOURS = [9, 14, 19];

    /**
     * @param  list<array<string, mixed>>  $planDays
     */
    public function build(
        string $islandName,
        int $days,
        array $planDays,
        ?string $summary = null,
        ?CarbonInterface $tripStart = null,
    ): string {
        $tripStart ??= Carbon::now(self::TIMEZONE)->addWeek()->startOfDay();

        $details = $this->formatItineraryDetails($planDays, $summary, $days, $islandName);

        if ($planDays === []) {
            $start = $tripStart->copy()->setTime(9, 0);
            $end = $start->copy()->addHours(2);

            return $this->templateUrl(
                text: "AfterArrival · {$days}-day plan — {$islandName}",
                start: $start,
                end: $end,
                details: $details,
                location: "{$islandName}, Maldives",
            );
        }

        [$start, $end, $title] = $this->firstEventWindow($planDays, $tripStart, $islandName);

        return $this->templateUrl($title, $start, $end, $details, "{$islandName}, Maldives");
    }

    /**
     * @param  list<array<string, mixed>>  $planDays
     * @return array{0: CarbonInterface, 1: CarbonInterface, 2: string}
     */
    protected function firstEventWindow(array $planDays, CarbonInterface $tripStart, string $islandName): array
    {
        foreach ($planDays as $dayBlock) {
            $dayNum = max(1, (int) ($dayBlock['day'] ?? 1));
            $dayDate = $tripStart->copy()->addDays($dayNum - 1);
            $items = $dayBlock['items'] ?? [];

            foreach ($items as $index => $item) {
                $listing = $item['listing'] ?? [];
                $title = (string) ($listing['title'] ?? 'Activity');
                $note = isset($item['note']) ? (string) $item['note'] : null;
                $hour = self::SLOT_HOURS[$index % count(self::SLOT_HOURS)];
                $start = $dayDate->copy()->setTime($hour, 0);
                $end = $start->copy()->addHours(2);
                $eventTitle = $note !== '' && $note !== null
                    ? "{$title} — {$note}"
                    : $title;

                return [$start, $end, "{$eventTitle} · {$islandName}"];
            }
        }

        $start = $tripStart->copy()->setTime(9, 0);

        return [$start, $start->copy()->addHours(2), "AfterArrival · {$islandName}"];
    }

    /**
     * @param  list<array<string, mixed>>  $planDays
     */
    protected function formatItineraryDetails(
        array $planDays,
        ?string $summary,
        int $days,
        string $islandName,
    ): string {
        $lines = [];

        if ($summary !== null && $summary !== '') {
            $lines[] = $summary;
            $lines[] = '';
        }

        if ($planDays === []) {
            $lines[] = "{$days}-day AfterArrival plan on {$islandName}.";

            return implode("\n", $lines);
        }

        $lines[] = 'Full itinerary (demo export):';

        foreach ($planDays as $dayBlock) {
            $day = $dayBlock['day'] ?? '?';
            $dayTitle = $dayBlock['title'] ?? 'Island day';
            $lines[] = "Day {$day}: {$dayTitle}";

            foreach ($dayBlock['items'] ?? [] as $item) {
                $listing = $item['listing'] ?? [];
                $title = $listing['title'] ?? 'Activity';
                $note = $item['note'] ?? null;
                $price = isset($listing['price_usd'])
                    ? ' · $'.number_format((float) $listing['price_usd'], 0)
                    : '';
                $lines[] = '  • '.$title.($note ? " ({$note})" : '').$price;
            }
        }

        $lines[] = '';
        $lines[] = 'Demo — opens Google Calendar to add your first activity; full plan is in the event notes.';

        return implode("\n", $lines);
    }

    protected function templateUrl(
        string $text,
        CarbonInterface $start,
        CarbonInterface $end,
        string $details,
        string $location,
    ): string {
        $query = http_build_query([
            'action' => 'TEMPLATE',
            'text' => $text,
            'dates' => $start->format('Ymd\THis').'/'.$end->format('Ymd\THis'),
            'details' => $details,
            'location' => $location,
            'ctz' => self::TIMEZONE,
        ], '', '&', PHP_QUERY_RFC3986);

        return 'https://calendar.google.com/calendar/render?'.$query;
    }
}
