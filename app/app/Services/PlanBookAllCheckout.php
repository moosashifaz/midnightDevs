<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderBundle;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlanBookAllCheckout
{
    public function __construct(
        protected SwipeService $swipe,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{lineItems: Collection<int, array<string, mixed>>, totalUsd: float, totalMvr: float}
     */
    public function resolveLineItems(array $payload): array
    {
        $slugs = collect($payload['items'] ?? [])->pluck('slug')->filter()->unique()->values();

        $listingsBySlug = Listing::query()
            ->with('provider')
            ->whereIn('slug', $slugs)
            ->where('is_active', true)
            ->get()
            ->keyBy('slug');

        $lineItems = collect($payload['items'] ?? [])
            ->map(function (array $item) use ($listingsBySlug) {
                $listing = $listingsBySlug->get($item['slug'] ?? '');

                return [
                    ...$item,
                    'listing' => $listing,
                    'available' => $listing !== null,
                ];
            })
            ->values();

        return [
            'lineItems' => $lineItems,
            'totalUsd' => round($lineItems->sum(fn (array $row) => (float) ($row['price_usd'] ?? 0)), 2),
            'totalMvr' => round($lineItems->sum(fn (array $row) => (float) ($row['price_mvr'] ?? 0)), 2),
        ];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $lineItems
     */
    public function checkout(
        User $user,
        Collection $lineItems,
        ?string $specialRequests = null,
        ?string $scheduledFor = null,
    ): OrderBundle {
        $available = $lineItems->filter(fn (array $row) => ($row['available'] ?? false) && $row['listing'] instanceof Listing);

        if ($available->isEmpty()) {
            throw new \InvalidArgumentException('No available listings to book.');
        }

        return DB::transaction(function () use ($user, $available, $specialRequests, $scheduledFor) {
            $totalMvr = $available->sum(fn (array $row) => (float) $row['listing']->price_mvr);
            $totalUsd = $available->sum(fn (array $row) => (float) $row['listing']->price_usd);

            $bundle = OrderBundle::create([
                'user_id' => $user->id,
                'item_count' => $available->count(),
                'amount_mvr' => $totalMvr,
                'amount_usd' => $totalUsd,
                'currency' => 'MVR',
                'status' => OrderBundle::STATUS_PENDING,
                'special_requests' => $specialRequests,
                'scheduled_for' => $scheduledFor,
            ]);

            $orders = $available->map(function (array $row) use ($user, $bundle, $scheduledFor) {
                /** @var Listing $listing */
                $listing = $row['listing'];

                return Order::create([
                    'user_id' => $user->id,
                    'order_bundle_id' => $bundle->id,
                    'listing_id' => $listing->id,
                    'provider_id' => $listing->provider_id,
                    'amount_mvr' => $listing->price_mvr,
                    'amount_usd' => $listing->price_usd,
                    'currency' => 'MVR',
                    'status' => Order::STATUS_PENDING,
                    'special_requests' => $row['note'] ?? null,
                    'scheduled_for' => $scheduledFor,
                ]);
            });

            $payment = $this->swipe->createBundleCharge($bundle, $orders);

            if ($payment->status === \App\Models\Payment::STATUS_COMPLETED) {
                $bundle->update(['status' => OrderBundle::STATUS_PAID]);
                $orders->each(function (Order $order) {
                    $order->update(['status' => Order::STATUS_PAID]);
                    $order->load('listing');
                    $order->listing?->increment('order_count');
                });
            }

            return $bundle->load(['orders.listing.provider', 'payment']);
        });
    }
}
