<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\SwipeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $provider = $request->user()->provider;

        abort_if(! $provider, 403, 'No provider profile associated with this account.');

        $stats = [
            'pending' => Order::where('provider_id', $provider->id)->where('status', Order::STATUS_PAID)->count(),
            'fulfilled' => Order::where('provider_id', $provider->id)->whereIn('status', [Order::STATUS_FULFILLED, Order::STATUS_COMPLETED])->count(),
            'gross_mvr' => Order::where('provider_id', $provider->id)->where('status', Order::STATUS_COMPLETED)->sum('amount_mvr'),
        ];

        $recentOrders = Order::with('listing', 'user')
            ->where('provider_id', $provider->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('provider.dashboard', [
            'provider' => $provider,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function fulfill(Request $request, Order $order, SwipeService $swipe): RedirectResponse
    {
        $provider = $request->user()->provider;
        abort_if(! $provider || $order->provider_id !== $provider->id, 403);

        $request->validate(['voucher_code' => 'required|string']);

        if (strcasecmp(trim($request->voucher_code), $order->voucher_code) !== 0) {
            return back()->withErrors(['voucher_code' => 'Voucher code does not match this order.']);
        }

        $order->update([
            'status' => Order::STATUS_FULFILLED,
            'fulfilled_at' => now(),
        ]);

        if ($order->payment) {
            $swipe->releaseEscrow($order->payment);
        }

        $order->update([
            'status' => Order::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return back()->with('status', 'Voucher verified. Order marked complete. Payout will arrive in this week\'s Swipe batch.');
    }
}
