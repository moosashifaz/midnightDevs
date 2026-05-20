<?php

use App\Livewire\PlanBuilder;
use App\Models\Island;
use App\Models\SavedPlan;
use App\Models\User;
use App\Services\SamplePlanService;
use Livewire\Livewire;

test('guest cannot view saved plans index', function () {
    $this->get(route('plans.index'))
        ->assertRedirect(route('login'));
});

test('authenticated user can save a generated plan', function () {
    $island = Island::create([
        'name' => 'Maafushi',
        'slug' => 'maafushi',
        'atoll' => 'South Male',
        'is_pilot' => true,
    ]);

    $user = User::factory()->create([
        'current_island_id' => $island->id,
    ]);

    $sample = app(SamplePlanService::class)->forIsland($island, 2000, 5);

    Livewire::actingAs($user)
        ->test(PlanBuilder::class)
        ->set('budgetUsd', $sample->budgetUsd)
        ->set('days', $sample->dayCount)
        ->set('spentUsd', $sample->spentUsd)
        ->set('summary', $sample->summary)
        ->set('planDays', $sample->planDays)
        ->set('hasPlan', true)
        ->call('savePlan');

    $this->assertDatabaseHas('saved_plans', [
        'user_id' => $user->id,
        'island_id' => $island->id,
        'budget_usd' => 2000,
        'days' => 5,
    ]);

    expect(SavedPlan::where('user_id', $user->id)->count())->toBe(1);
});

test('user cannot view another users saved plan', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $plan = SavedPlan::create([
        'user_id' => $owner->id,
        'budget_usd' => 500,
        'days' => 3,
        'spent_usd' => 300,
        'summary' => 'Test plan',
        'plan_data' => [],
    ]);

    $this->actingAs($other)
        ->get(route('plans.show', $plan))
        ->assertForbidden();
});
