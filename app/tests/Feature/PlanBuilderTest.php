<?php

use App\Livewire\PlanBuilder;
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
    $this->get(route('plan', ['budget' => 1500, 'days' => 3, 'generate' => 1]))
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
        ->assertDontSee('Example plan');
});
