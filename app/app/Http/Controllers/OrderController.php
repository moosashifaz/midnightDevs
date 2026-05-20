<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Order;
use App\Services\SwipeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(Listing $listing): View
    {
        return view('orders.checkout', [
            'listing' => $listing->load('provider'),
        ]);
    }

    public function store(Request $request, Listing $listing, SwipeService $swipe): RedirectResponse
    {
        $request->validate([
            'special_requests' => ['nullable', 'string', 'max:500'],
            'scheduled_for' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $order = DB::transaction(function () use ($request, $listing, $swipe) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'listing_id' => $listing->id,
                'provider_id' => $listing->provider_id,
                'amount_mvr' => $listing->price_mvr,
                'amount_usd' => $listing->price_usd,
                'currency' => 'MVR',
                'status' => Order::STATUS_PENDING,
                'scheduled_for' => $request->input('scheduled_for'),
                'special_requests' => $request->input('special_requests'),
            ]);

            $swipe->createCharge($order);

            $order->update(['status' => Order::STATUS_PAID]);
            $listing->increment('order_count');

            return $order;
        });

        return redirect()->route('orders.show', $order)
            ->with('status', 'Payment confirmed via BML Swipe. Show your voucher to the provider when you collect.');
    }

    public function show(Order $order): View
    {
        abort_if($order->user_id !== auth()->id() && ! auth()->user()->isAdmin(), 403);

        $order->load(['listing.provider.island', 'payment']);

        return view('orders.show', ['order' => $order]);
    }

    public function index(Request $request): View
    {
        $orders = Order::with(['listing', 'provider'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('orders.index', ['orders' => $orders]);
    }
}
