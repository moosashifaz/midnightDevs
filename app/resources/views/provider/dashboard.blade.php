<x-marketplace-layout>
    <div class="flex items-end justify-between mb-6">
        <div>
            <p class="text-xs uppercase tracking-wider text-cyan-600 font-medium">Provider portal</p>
            <h1 class="text-2xl font-bold">{{ $provider->business_name }}</h1>
            <p class="text-sm text-gray-500">{{ $provider->island->name }} · {{ Str::headline($provider->category) }} ·
                <span class="inline-flex items-center gap-1 text-emerald-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> {{ Str::headline($provider->verification_status) }}
                </span>
            </p>
        </div>
        <div class="text-right text-sm">
            <div class="text-gray-500">Rating</div>
            <div class="text-xl font-bold text-amber-500">★ {{ number_format($provider->rating, 1) }}</div>
        </div>
    </div>

    <section class="grid sm:grid-cols-3 gap-3 mb-8">
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <div class="text-xs uppercase tracking-wider text-gray-500">Awaiting fulfillment</div>
            <div class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</div>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <div class="text-xs uppercase tracking-wider text-gray-500">Fulfilled / completed</div>
            <div class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['fulfilled'] }}</div>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
            <div class="text-xs uppercase tracking-wider text-gray-500">Gross revenue (MVR)</div>
            <div class="text-3xl font-bold text-cyan-600 mt-1">MVR {{ number_format($stats['gross_mvr'], 0) }}</div>
        </div>
    </section>

    <section>
        <h2 class="font-bold mb-3">Recent orders</h2>

        @if($recentOrders->isEmpty())
            <div class="rounded-2xl bg-gray-50 dark:bg-gray-800 p-10 text-center text-gray-500">
                No orders yet. Once tourists buy your listings, they'll appear here for fulfillment.
            </div>
        @else
            <div class="space-y-3">
                @foreach($recentOrders as $order)
                    <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="text-sm font-semibold">{{ $order->listing->title }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $order->reference }} · {{ $order->user->name }} · {{ $order->created_at->diffForHumans() }}
                                </div>
                                @if($order->special_requests)
                                    <div class="text-xs text-gray-700 dark:text-gray-300 mt-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2">
                                        <strong>Note:</strong> {{ $order->special_requests }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="font-bold">MVR {{ number_format($order->amount_mvr, 0) }}</div>
                                <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full
                                    @switch($order->status)
                                        @case('completed') bg-emerald-100 text-emerald-700 @break
                                        @case('fulfilled') bg-blue-100 text-blue-700 @break
                                        @case('paid') bg-amber-100 text-amber-700 @break
                                        @default bg-gray-100 text-gray-700
                                    @endswitch
                                ">{{ $order->status }}</span>
                            </div>
                        </div>

                        @if($order->isFulfillable() && ! $order->isCompleted())
                            <form method="POST" action="{{ route('provider.orders.fulfill', $order) }}" class="mt-3 flex gap-2">
                                @csrf
                                <input name="voucher_code" type="text" placeholder="Tourist's voucher code" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm" autocomplete="off">
                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium px-4 rounded-lg">
                                    Verify & complete
                                </button>
                            </form>
                            @error('voucher_code')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-marketplace-layout>
