<?php

use App\Livewire\PlanBuilder;
use App\Models\Island;
use App\Models\Listing;
use App\Models\OrderBundle;
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
        ->call('toggleSwapOptions', 0, 0)
        ->call('pickSwapOption', 0, 0, $second->id)
        ->assertSet('planDays.0.items.0.listing.id', $second->id)
        ->assertSet('spentUsd', 40.0)
        ->call('removePlanItem', 0, 0)
        ->assertSet('planDays.0.items', [])
        ->assertSet('spentUsd', 0.0);
});

test('book all stores plan items and shows checkout list', function () {
    $island = Island::create([
        'name' => 'Maafushi',
        'slug' => 'maafushi-book-all',
        'atoll' => 'South Male',
        'is_pilot' => true,
    ]);

    $owner = User::factory()->create();
    $provider = Provider::create([
        'user_id' => $owner->id,
        'island_id' => $island->id,
        'business_name' => 'Island Co',
        'slug' => 'island-co-ba',
        'category' => 'experience',
    ]);

    $listing = Listing::create([
        'provider_id' => $provider->id,
        'island_id' => $island->id,
        'title' => 'Sunset cruise',
        'slug' => 'sunset-cruise-ba',
        'category' => 'experience',
        'type' => 'experience',
        'price_mvr' => 500,
        'price_usd' => 45,
        'rating' => 4.5,
        'is_active' => true,
    ]);

    $user = User::factory()->create(['current_island_id' => $island->id]);

    $planDays = [
        [
            'day' => 1,
            'title' => 'Day one',
            'items' => [
                ['listing' => PlanResult::listingSnapshot($listing), 'note' => 'Evening'],
            ],
        ],
    ];

    $sessionPayload = [
        'island_name' => 'Maafushi',
        'summary' => 'Test plan',
        'spent_usd' => 45.0,
        'items' => [
            [
                'slug' => $listing->slug,
                'title' => 'Sunset cruise',
                'note' => 'Evening',
                'day' => 1,
                'price_usd' => 45.0,
                'price_mvr' => 500.0,
            ],
        ],
        'return_url' => route('plan'),
    ];

    $this->actingAs($user)
        ->withSession(['plan_book_all' => $sessionPayload])
        ->get(route('plan.book-all'))
        ->assertOk()
        ->assertSee('Book all in one checkout')
        ->assertSee('Sunset cruise')
        ->assertSee('one checkout')
        ->assertSee('plan/book-all', false);

    $this->actingAs($user)
        ->withSession(['plan_book_all' => $sessionPayload])
        ->post(route('plan.book-all.store'), [
            'special_requests' => 'Evening preference',
        ])
        ->assertRedirect();

    $bundle = OrderBundle::with(['orders', 'payment'])->first();
    expect($bundle)->not->toBeNull();

    $this->actingAs($user)
        ->get(route('orders.bundle.show', $bundle))
        ->assertOk()
        ->assertSee($bundle->reference);
    expect($bundle->item_count)->toBe(1)
        ->and($bundle->orders)->toHaveCount(1)
        ->and($bundle->payment)->not->toBeNull();
});

test('user can open swap bar and pick a plan item', function () {
    $island = Island::create([
        'name' => 'Maafushi',
        'slug' => 'maafushi-choose',
        'atoll' => 'South Male',
        'is_pilot' => true,
    ]);

    $owner = User::factory()->create();
    $provider = Provider::create([
        'user_id' => $owner->id,
        'island_id' => $island->id,
        'business_name' => 'Island Co',
        'slug' => 'island-co-choose',
        'category' => 'experience',
    ]);

    $first = Listing::create([
        'provider_id' => $provider->id,
        'island_id' => $island->id,
        'title' => 'Snorkel A',
        'slug' => 'snorkel-a-choose',
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
        'slug' => 'snorkel-b-choose',
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
        ->call('toggleSwapOptions', 0, 0)
        ->assertSet('swapBarDayIndex', 0)
        ->assertSet('swapBarItemIndex', 0)
        ->assertCount('swapBarOptions', 2)
        ->call('pickSwapOption', 0, 0, $second->id)
        ->assertSet('planDays.0.items.0.listing.id', $second->id)
        ->assertSet('swapBarDayIndex', null)
        ->assertSet('spentUsd', 40.0);
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
