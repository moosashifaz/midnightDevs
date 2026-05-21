<?php

use App\Livewire\PlanBuilder;
use App\Models\Island;
use App\Models\Listing;
use App\Models\Provider;
use App\Models\User;
use App\Services\Planner\PlanResult;
use App\Services\SamplePlanService;
use Livewire\Livewire;

test('plan page is accessible to guests', function () {
    $this->get(route('plan'))
        ->assertOk()
        ->assertSee('AI island planner');
});

test('sample plan service returns structured days within budget', function () {
    $service = app(SamplePlanService::class);
    $plan = $service->forIsland(null, 2000, 5);

    expect($plan)->toBeInstanceOf(PlanResult::class)
        ->and($plan->budgetUsd)->toBe(2000.0)
        ->and($plan->dayCount)->toBe(5)
        ->and($plan->spentUsd)->toBeLessThanOrEqual(2000)
        ->and($plan->isSample)->toBeTrue();
});

test('guest with generate param is redirected to login', function () {
    $this->get(route('plan', [
        'budget' => 1500,
        'days' => 3,
        'interests' => 'snorkeling,local-food',
        'generate' => 1,
    ]))
        ->assertRedirect(route('login'));
});

test('authenticated user starts without a plan until they generate', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PlanBuilder::class)
        ->assertSet('budgetUsd', 2000)
        ->assertSet('days', 5)
        ->assertSet('hasPlan', false)
        ->assertSet('planDays', [])
        ->assertSee('Ready when you are')
        ->assertOk();
});

test('home page shows ai planner teaser without pre-built itinerary', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Plan your island stay')
        ->assertSee('Tap an amount to set your budget')
        ->assertSee('What are you into?')
        ->assertDontSee('Example plan');
});

test('plan builder loads interests from query string', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->withQueryParams(['interests' => 'snorkeling,dolphins', 'budget' => 1000, 'days' => 3])
        ->test(PlanBuilder::class)
        ->assertSet('interests', ['snorkeling', 'dolphins']);
});

test('user can swap and remove plan items', function () {
    $island = Island::create([
        'name' => 'Maafushi',
        'slug' => 'maafushi-swap',
        'atoll' => 'South Male',
        'is_pilot' => true,
    ]);

    $owner = User::factory()->create();
    $provider = Provider::create([
        'user_id' => $owner->id,
        'island_id' => $island->id,
        'business_name' => 'Island Co',
        'slug' => 'island-co',
        'category' => 'experience',
    ]);

    $first = Listing::create([
        'provider_id' => $provider->id,
        'island_id' => $island->id,
        'title' => 'Snorkel A',
        'slug' => 'snorkel-a',
        'category' => 'experience',
        'type' => 'experience',
        'price_mvr' => 400,
        'price_usd' => 30,
        'rating' => 4.8,
        'is_active' => true,
    ]);

    $second = Listing::create([
        'provider_id' => $provider->id,
        'island_id' => $island->id,
        'title' => 'Snorkel B',
        'slug' => 'snorkel-b',
        'category' => 'experience',
        'type' => 'experience',
        'price_mvr' => 500,
        'price_usd' => 40,
        'rating' => 4.2,
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'current_island_id' => $island->id,
    ]);

    $planDays = [
        [
            'day' => 1,
            'title' => 'Reef day',
            'items' => [
                ['listing' => PlanResult::listingSnapshot($first), 'note' => 'Morning'],
            ],
        ],
    ];

    Livewire::actingAs($user)
        ->test(PlanBuilder::class)
        ->set('hasPlan', true)
        ->set('planIslandId', $island->id)
        ->set('budgetUsd', 500)
        ->set('spentUsd', 30)
        ->set('planDays', $planDays)
        ->assertSee('Swap')
        ->assertSee('Remove')
        ->call('swapPlanItem', 0, 0)
        ->assertSet('planDays.0.items.0.listing.id', $second->id)
        ->assertSet('spentUsd', 40.0)
        ->call('removePlanItem', 0, 0)
        ->assertSet('planDays.0.items', [])
        ->assertSet('spentUsd', 0.0);
});

test('plan builder shows fullscreen building overlay while generating', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PlanBuilder::class)
        ->set('isGenerating', true)
        ->assertSee('Creating your island plan')
        ->assertSee('Matching live listings')
        ->assertSeeHtml('data-planner-lottie')
        ->assertDontSee('Ready when you are');
});
