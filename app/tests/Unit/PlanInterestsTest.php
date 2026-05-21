<?php

use App\Models\Listing;
use App\Services\Planner\PlanInterests;

test('fromQuery defaults to all activity interests when empty', function () {
    $interests = PlanInterests::fromQuery(null);

    expect($interests->included)->toBe(PlanInterests::ALL)
        ->and($interests->isAll())->toBeTrue();
});

test('fromQuery parses selected activities', function () {
    $interests = PlanInterests::fromQuery('snorkeling,local-food');

    expect($interests->included)->toBe(['snorkeling', 'local-food'])
        ->and($interests->contains('diving'))->toBeFalse();
});

test('empty selection falls back to all activities', function () {
    $interests = PlanInterests::fromArray([]);

    expect($interests->included)->toBe(PlanInterests::ALL);
});

test('matches snorkeling listings by keywords', function () {
    $interests = PlanInterests::fromArray(['snorkeling']);

    $match = new Listing([
        'title' => 'Snorkel with Sea Turtles',
        'description' => 'Guided reef trip',
        'category' => 'experience',
    ]);

    $noMatch = new Listing([
        'title' => 'Standard Laundry (per kg)',
        'description' => 'Wash and fold',
        'category' => 'wash',
    ]);

    expect($interests->matchesListing($match))->toBeTrue()
        ->and($interests->matchesListing($noMatch))->toBeFalse();
});

test('prompt focus line references activity labels not categories', function () {
    $interests = PlanInterests::fromArray(['snorkeling', 'diving']);

    expect($interests->promptFocusLine())
        ->toContain('Snorkeling')
        ->toContain('Diving')
        ->not->toContain('Taste');
});
