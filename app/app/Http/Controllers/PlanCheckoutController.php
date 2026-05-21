<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\OrderBundle;
use App\Models\SavedPlan;
use App\Services\PlanBookAllCheckout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanCheckoutController extends Controller
{
    public function __construct(
        protected PlanBookAllCheckout $checkout,
    ) {
    }

    public function bookAll(Request $request): View|RedirectResponse
    {
        $payload = $request->session()->get('plan_book_all');

        if (! is_array($payload) || empty($payload['items'])) {
            return redirect()
                ->route('plan')
                ->with('error', 'Generate a plan first, then use Book all.');
        }

        return $this->renderBookAll($payload);
    }

    public function storeBookAll(Request $request): RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $payload = $request->session()->get('plan_book_all');

        if (! is_array($payload) || empty($payload['items'])) {
            return redirect()
                ->route('plan')
                ->with('error', 'Your plan session expired — open Book all again from your plan.');
        }

        $validated = $request->validate([
            'special_requests' => ['nullable', 'string', 'max:500'],
            'scheduled_for' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $resolved = $this->checkout->resolveLineItems($payload);
        $available = $resolved['lineItems']->filter(fn (array $row) => $row['available'] ?? false);

        if ($available->isEmpty()) {
            return redirect()
                ->route('plan.book-all')
                ->with('error', 'None of these listings are still available. Update your plan and try again.');
        }

        try {
            $bundle = $this->checkout->checkout(
                $request->user(),
                $available,
                $validated['special_requests'] ?? null,
                $validated['scheduled_for'] ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('plan.book-all')
                ->with('error', $e->getMessage());
        }

        $request->session()->forget('plan_book_all');

        $paymentUrl = data_get($bundle->payment?->swipe_payload ?? [], 'payment_url');

        if ($paymentUrl) {
            return redirect()->away($paymentUrl);
        }

        return redirect()
            ->route('orders.bundle.show', $bundle)
            ->with('status', 'Plan booked in one payment. Show each voucher to providers when you redeem.');
    }

    public function showBundle(Request $request, OrderBundle $bundle): View
    {
        abort_unless($bundle->user_id === $request->user()->id, 403);

        $bundle->load(['orders.listing.provider', 'orders.listing.island', 'payment']);

        return view('orders.bundle-show', [
            'bundle' => $bundle,
        ]);
    }

    public function bookAllFromSaved(Request $request, SavedPlan $savedPlan): RedirectResponse
    {
        abort_unless($savedPlan->user_id === $request->user()->id, 403);

        $items = $this->itemsFromPlanDays($savedPlan->plan_data ?? []);

        if ($items === []) {
            return redirect()
                ->route('plans.show', $savedPlan)
                ->with('error', 'This plan has no activities to book.');
        }

        $savedPlan->load('island');

        $request->session()->put('plan_book_all', [
            'island_name' => $savedPlan->island?->name,
            'summary' => $savedPlan->summary,
            'spent_usd' => (float) $savedPlan->spent_usd,
            'items' => $items,
            'return_url' => route('plans.show', $savedPlan),
        ]);

        return redirect()->route('plan.book-all');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function renderBookAll(array $payload): View
    {
        $resolved = $this->checkout->resolveLineItems($payload);

        return view('marketplace.plan-book-all', [
            'islandName' => $payload['island_name'] ?? null,
            'summary' => $payload['summary'] ?? null,
            'totalUsd' => $resolved['totalUsd'],
            'totalMvr' => $resolved['totalMvr'],
            'lineItems' => $resolved['lineItems'],
            'returnUrl' => $payload['return_url'] ?? route('plan'),
            'itemCount' => $resolved['lineItems']->count(),
            'availableCount' => $resolved['lineItems']->filter(fn (array $row) => $row['available'] ?? false)->count(),
        ]);
    }

    /**
     * @param  list<array<string, mixed>>|array<string, mixed>  $planDays
     * @return list<array{slug: string, title: string, note: string|null, day: int|null, price_usd: float, price_mvr: float}>
     */
    protected function itemsFromPlanDays(array $planDays): array
    {
        $items = [];

        foreach ($planDays as $dayBlock) {
            if (! is_array($dayBlock)) {
                continue;
            }

            foreach ($dayBlock['items'] ?? [] as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $listing = $item['listing'] ?? [];
                $slug = $listing['slug'] ?? null;

                if (! $slug) {
                    continue;
                }

                $items[] = [
                    'slug' => (string) $slug,
                    'title' => (string) ($listing['title'] ?? 'Listing'),
                    'note' => isset($item['note']) ? (string) $item['note'] : null,
                    'day' => isset($dayBlock['day']) ? (int) $dayBlock['day'] : null,
                    'price_usd' => (float) ($listing['price_usd'] ?? 0),
                    'price_mvr' => (float) ($listing['price_mvr'] ?? 0),
                ];
            }
        }

        return $items;
    }
}
