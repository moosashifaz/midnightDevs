<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

use App\Models\Island;
use App\Models\Listing;
use App\Models\Provider;
use App\Models\User;
use App\Services\Planner\PlanInterests;
use App\Services\Planner\PlanItemAlternatives;
use App\Services\Planner\PlanResult;

test('next swap cycles through same-category candidates', function () {
    $island = Island::create([
        'name' => 'Test',
        'slug' => 'test-island',
        'atoll' => 'Test',
        'is_pilot' => true,
    ]);

    $owner = User::factory()->create();
    $provider = Provider::create([
        'user_id' => $owner->id,
        'island_id' => $island->id,
        'business_name' => 'Test Provider',
        'slug' => 'test-provider',
        'category' => 'experience',
    ]);

    $a = Listing::create([
        'provider_id' => $provider->id,
        'island_id' => $island->id,
        'title' => 'Cruise A',
        'slug' => 'cruise-a',
        'category' => 'experience',
        'type' => 'experience',
        'price_mvr' => 500,
        'price_usd' => 40,
        'rating' => 4.5,
        'is_active' => true,
    ]);

    $b = Listing::create([
        'provider_id' => $provider->id,
        'island_id' => $island->id,
        'title' => 'Cruise B',
        'slug' => 'cruise-b',
        'category' => 'experience',
        'type' => 'experience',
        'price_mvr' => 600,
        'price_usd' => 50,
        'rating' => 4.0,
        'is_active' => true,
    ]);

    $planDays = [
        [
            'day' => 1,
            'title' => 'Day one',
            'items' => [
                ['listing' => PlanResult::listingSnapshot($a), 'note' => null],
            ],
        ],
    ];

    $swapper = new PlanItemAlternatives;
    $candidates = $swapper->forSlot($island, $planDays, 0, 0, 500);

    expect($candidates->pluck('id')->all())->toContain($b->id);

    $next = $swapper->nextSwap($a, $candidates);

    expect($next?->id)->toBe($b->id);
});
