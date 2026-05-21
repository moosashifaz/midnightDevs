<?php

namespace App\Livewire;

use App\Models\Island;
use App\Models\Listing;
use App\Models\SavedPlan;
use App\Services\AIPlannerService;
use App\Services\Planner\PlanGoogleCalendarUrl;
use App\Services\Planner\PlanInterests;
use App\Services\Planner\PlanItemAlternatives;
use App\Services\Planner\PlanResult;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
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

    /** @var list<string> */
    public array $interests = PlanInterests::ALL;

    public ?int $planIslandId = null;

    public ?string $planMessage = null;

    public ?int $chooserDayIndex = null;

    public ?int $chooserItemIndex = null;

    /** @var list<array{listing: array<string, mixed>, is_current: bool}> */
    public array $chooserOptions = [];

    public function mount(Request $request): void
    {
        $this->budgetUsd = max(100, (float) $request->query('budget', 2000));
        $this->days = max(1, min(7, (int) $request->query('days', 5)));
        $this->interests = PlanInterests::fromQuery($request->query('interests'))->included;

        $island = $this->resolveIsland($request);
        $this->islandName = $island?->name;
        $this->planIslandId = $island?->id;

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

    public function swapPlanItem(int $dayIndex, int $itemIndex): void
    {
        if (! $this->hasPlan || ! isset($this->planDays[$dayIndex]['items'][$itemIndex])) {
            return;
        }

        $island = $this->planIsland();
        if (! $island) {
            $this->planMessage = 'Select an island first, then regenerate your plan.';

            return;
        }

        $listingId = (int) ($this->planDays[$dayIndex]['items'][$itemIndex]['listing']['id'] ?? 0);
        $current = Listing::query()->with('provider')->find($listingId);
        if (! $current) {
            $this->planMessage = 'Could not load this listing. Try regenerating your plan.';

            return;
        }

        $swapper = app(PlanItemAlternatives::class);
        $candidates = $swapper->forSlot(
            $island,
            $this->planDays,
            $dayIndex,
            $itemIndex,
            $this->budgetUsd,
        );

        $next = $swapper->nextSwap($current, $candidates);
        if (! $next) {
            $this->planMessage = 'No other options in this category within your budget — try Remove or Regenerate.';

            return;
        }

        $this->applyListingToSlot($dayIndex, $itemIndex, $next);
        $this->closeChooser();
        $this->planMessage = 'Swapped to '.$next->title.'.';
    }

    public function toggleChooseOptions(int $dayIndex, int $itemIndex): void
    {
        if ($this->chooserDayIndex === $dayIndex && $this->chooserItemIndex === $itemIndex) {
            $this->closeChooser();

            return;
        }

        if (! $this->hasPlan || ! isset($this->planDays[$dayIndex]['items'][$itemIndex])) {
            return;
        }

        $island = $this->planIsland();
        if (! $island) {
            $this->planMessage = 'Select an island first, then regenerate your plan.';

            return;
        }

        $options = app(PlanItemAlternatives::class)->optionsForSlot(
            $island,
            $this->planDays,
            $dayIndex,
            $itemIndex,
            $this->budgetUsd,
        );

        if (count($options) <= 1) {
            $this->planMessage = 'No other options in this category within your budget — try Remove or Regenerate.';

            return;
        }

        $this->chooserDayIndex = $dayIndex;
        $this->chooserItemIndex = $itemIndex;
        $this->chooserOptions = $options;
        $this->planMessage = null;
    }

    public function closeChooser(): void
    {
        $this->chooserDayIndex = null;
        $this->chooserItemIndex = null;
        $this->chooserOptions = [];
    }

    public function pickPlanItem(int $dayIndex, int $itemIndex, int $listingId): void
    {
        if ($this->chooserDayIndex !== $dayIndex || $this->chooserItemIndex !== $itemIndex) {
            return;
        }

        $allowed = collect($this->chooserOptions)
            ->first(fn (array $option) => (int) ($option['listing']['id'] ?? 0) === $listingId);

        if (! $allowed) {
            return;
        }

        $listing = Listing::query()->with('provider')->find($listingId);
        if (! $listing) {
            return;
        }

        $this->applyListingToSlot($dayIndex, $itemIndex, $listing);
        $this->closeChooser();
        $this->planMessage = 'Chosen: '.$listing->title.'.';
    }

    public function removePlanItem(int $dayIndex, int $itemIndex): void
    {
        if (! $this->hasPlan || ! isset($this->planDays[$dayIndex]['items'][$itemIndex])) {
            return;
        }

        $planDays = $this->planDays;
        $items = $planDays[$dayIndex]['items'];
        array_splice($items, $itemIndex, 1);
        $planDays[$dayIndex]['items'] = array_values($items);
        $this->planDays = $planDays;

        $this->recalculateSpent();
        $this->closeChooser();
        $this->planMessage = 'Removed from your plan.';
    }

    protected function applyListingToSlot(int $dayIndex, int $itemIndex, Listing $listing): void
    {
        $note = $this->planDays[$dayIndex]['items'][$itemIndex]['note'] ?? null;

        $planDays = $this->planDays;
        $planDays[$dayIndex]['items'][$itemIndex] = [
            'listing' => PlanResult::listingSnapshot($listing),
            'note' => $note,
        ];
        $this->planDays = $planDays;

        $this->recalculateSpent();
    }

    #[Computed]
    public function googleCalendarUrl(): string
    {
        return app(PlanGoogleCalendarUrl::class)->build(
            islandName: $this->islandName ?? 'Maldives',
            days: $this->days,
            planDays: $this->planDays,
            summary: $this->summary,
        );
    }

    public function generate(Request $request, AIPlannerService $planner): void
    {
        $this->validate([
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => ['string', 'in:'.implode(',', PlanInterests::ALL)],
        ]);

        if (! $request->user()) {
            session()->put('url.intended', route('plan', [
                'budget' => $this->budgetUsd,
                'days' => $this->days,
                'interests' => PlanInterests::fromArray($this->interests)->toQueryString(),
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

        $prefs = PlanInterests::fromArray($this->interests);
        $matchCount = $prefs->filterListings(
            \App\Models\Listing::query()
                ->where('island_id', $island->id)
                ->where('is_active', true)
                ->get(),
        )->count();

        if ($matchCount === 0) {
            $this->error = 'No listings match your selected activities on this island. Try ticking more interests.';
            $this->isGenerating = false;

            return;
        }

        $result = $planner->generate(
            $island,
            $this->budgetUsd,
            $this->days,
            $request->user(),
            $prefs,
        );
        $this->applyPlan($result);
        $this->planIslandId = $island->id;
        $this->hasPlan = true;
        $this->isGenerating = false;
        $this->closeChooser();
    }

    protected function applyPlan(PlanResult $plan): void
    {
        $this->budgetUsd = $plan->budgetUsd;
        $this->days = $plan->dayCount;
        $this->spentUsd = $plan->spentUsd;
        $this->summary = $plan->summary;
        $this->planDays = $plan->planDays;
    }

    protected function recalculateSpent(): void
    {
        $this->spentUsd = app(PlanItemAlternatives::class)->spentUsd($this->planDays);
    }

    protected function planIsland(): ?Island
    {
        return $this->planIslandId
            ? Island::find($this->planIslandId)
            : null;
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
