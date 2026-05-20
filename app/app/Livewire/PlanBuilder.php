<?php

namespace App\Livewire;

use App\Models\Island;
use App\Models\SavedPlan;
use App\Services\AIPlannerService;
use App\Services\Planner\PlanResult;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.marketplace-layout')]
class PlanBuilder extends Component
{
    public float $budgetUsd = 2000;

    public int $days = 5;

    public ?string $summary = null;

    public float $spentUsd = 0;

    /** @var list<array<string, mixed>> */
    public array $planDays = [];

    public bool $hasPlan = false;

    public bool $isGenerating = false;

    public bool $autoGenerate = false;

    public ?string $error = null;

    public ?string $islandName = null;

    public function mount(Request $request): void
    {
        $this->budgetUsd = max(100, (float) $request->query('budget', 2000));
        $this->days = max(1, min(7, (int) $request->query('days', 5)));

        $island = $this->resolveIsland($request);
        $this->islandName = $island?->name;

        if ($request->boolean('generate')) {
            if (! $request->user()) {
                session()->put('url.intended', url()->full());

                $this->redirect(route('login'), navigate: true);

                return;
            }

            // Show building UI on first paint, then generate after Livewire mounts.
            $this->autoGenerate = true;
            $this->isGenerating = true;
        }
    }

    public function runQueuedGenerate(Request $request, AIPlannerService $planner): void
    {
        if (! $this->autoGenerate) {
            return;
        }

        $this->autoGenerate = false;
        $this->generate($request, $planner);
    }

    public function savePlan(Request $request): void
    {
        if (! $request->user() || ! $this->hasPlan) {
            return;
        }

        $island = $this->resolveIsland($request);

        SavedPlan::create([
            'user_id' => $request->user()->id,
            'island_id' => $island?->id,
            'budget_usd' => $this->budgetUsd,
            'days' => $this->days,
            'spent_usd' => $this->spentUsd,
            'summary' => $this->summary,
            'plan_data' => $this->planDays,
        ]);

        session()->flash('status', 'Plan saved to My plans.');
    }

    public function generate(Request $request, AIPlannerService $planner): void
    {
        if (! $request->user()) {
            session()->put('url.intended', route('plan', [
                'budget' => $this->budgetUsd,
                'days' => $this->days,
                'generate' => 1,
            ]));

            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->isGenerating = true;
        $this->error = null;

        $island = $this->resolveIsland($request);

        if (! $island) {
            $this->error = 'Select an island first to generate a plan.';
            $this->isGenerating = false;

            return;
        }

        $result = $planner->generate($island, $this->budgetUsd, $this->days, $request->user());
        $this->applyPlan($result);
        $this->hasPlan = true;
        $this->isGenerating = false;
    }

    protected function applyPlan(PlanResult $plan): void
    {
        $this->budgetUsd = $plan->budgetUsd;
        $this->days = $plan->dayCount;
        $this->spentUsd = $plan->spentUsd;
        $this->summary = $plan->summary;
        $this->planDays = $plan->planDays;
    }

    protected function resolveIsland(Request $request): ?Island
    {
        if ($request->user()?->current_island_id) {
            return Island::find($request->user()->current_island_id);
        }

        if ($slug = session('island_slug')) {
            return Island::where('slug', $slug)->first();
        }

        return Island::where('is_pilot', true)->orderBy('id')->first();
    }

    public function render()
    {
        return view('livewire.plan-builder');
    }
}
