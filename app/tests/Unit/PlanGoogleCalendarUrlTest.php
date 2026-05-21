<?php

use App\Services\Planner\PlanGoogleCalendarUrl;
use Illuminate\Support\Carbon;

test('builds google calendar template url with first plan item', function () {
    $start = Carbon::parse('2026-06-01', 'Indian/Maldives')->startOfDay();

    $url = (new PlanGoogleCalendarUrl)->build(
        islandName: 'Maafushi',
        days: 2,
        planDays: [
            [
                'day' => 1,
                'title' => 'Arrival day',
                'items' => [
                    [
                        'listing' => ['title' => 'Sunset cruise', 'price_usd' => 45],
                        'note' => 'Evening',
                    ],
                ],
            ],
        ],
        summary: 'A relaxed first day.',
        tripStart: $start,
    );

    expect($url)
        ->toStartWith('https://calendar.google.com/calendar/render?')
        ->toContain('action=TEMPLATE')
        ->toContain('ctz=Indian%2FMaldives')
        ->toContain('Sunset')
        ->toContain('A%20relaxed%20first%20day');
});

test('builds fallback url when plan has no days', function () {
    $url = (new PlanGoogleCalendarUrl)->build(
        islandName: 'Maafushi',
        days: 3,
        planDays: [],
        tripStart: Carbon::parse('2026-06-01', 'Indian/Maldives')->startOfDay(),
    );

    expect($url)->toContain('action=TEMPLATE')->toContain('Maafushi');
});
