<x-marketplace-layout>
    <div class="flex items-end justify-between mb-6">
        <div>
            <p class="section-eyebrow">Provider portal</p>
            <h1 class="section-title">{{ $provider->business_name }}</h1>
            <p class="text-sm text-muraka-500 mt-1 flex flex-wrap items-center gap-2">
                {{ $provider->island->name }} · {{ Str::headline($provider->category) }} ·
                <span class="inline-flex items-center gap-1 text-ruh-700">
                    <span class="w-2 h-2 rounded-full bg-ruh-500"></span>
                    {{ Str::headline($provider->verification_status) }}
                </span>
            </p>
        </div>
        <div class="text-right text-sm">
            <div class="text-muraka-500">Rating</div>
            <div class="text-xl font-bold text-iru-600 inline-flex items-center gap-1">
                <x-icons.icon name="star" class="w-5 h-5 fill-iru-400 stroke-iru-500" />
                {{ number_format($provider->rating, 1) }}
            </div>
        </div>
    </div>

    <section class="grid sm:grid-cols-3 gap-3 mb-8">
        <x-ui.card class="p-5">
            <div class="flex items-center gap-2 text-muraka-500 mb-1">
                <x-icons.icon name="clock" class="w-4 h-4" />
                <span class="text-xs uppercase tracking-label">Awaiting fulfillment</span>
            </div>
            <div class="text-3xl font-bold text-dhooni-700">{{ $stats['pending'] }}</div>
        </x-ui.card>
        <x-ui.card class="p-5">
            <div class="flex items-center gap-2 text-muraka-500 mb-1">
                <x-icons.icon name="check-circle" class="w-4 h-4" />
                <span class="text-xs uppercase tracking-label">Fulfilled / completed</span>
            </div>
            <div class="text-3xl font-bold text-ruh-700">{{ $stats['fulfilled'] }}</div>
        </x-ui.card>
        <x-ui.card class="p-5">
            <div class="flex items-center gap-2 text-muraka-500 mb-1">
                <x-icons.icon name="credit-card" class="w-4 h-4" />
                <span class="text-xs uppercase tracking-label">Gross revenue (MVR)</span>
            </div>
            <div class="text-3xl font-bold text-madi-700">MVR {{ number_format($stats['gross_mvr'], 0) }}</div>
        </x-ui.card>
    </section>

    <section>
        <h2 class="section-title mb-3">Recent orders</h2>

        @if($recentOrders->isEmpty())
            <x-ui.card class="p-10 text-center text-muraka-500">
                No orders yet. Once tourists buy your listings, they'll appear here for fulfillment.
            </x-ui.card>
        @else
            <div class="space-y-3">
                @foreach($recentOrders as $order)
                    <x-ui.card class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-muraka-900">{{ $order->listing->title }}</div>
                                <div class="text-xs text-muraka-500">
                                    {{ $order->reference }} · {{ $order->user->name }} · {{ $order->created_at->diffForHumans() }}
                                </div>
                                @if($order->special_requests)
                                    <div class="text-xs text-muraka-700 mt-2 bg-dhooni-50 border border-dhooni-200 rounded-lg px-3 py-2">
                                        <strong>Note:</strong> {{ $order->special_requests }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-bold text-madi-700">MVR {{ number_format($order->amount_mvr, 0) }}</div>
                                @php
                                    $statusClass = match($order->status) {
                                        'completed' => 'bg-ruh-100 text-ruh-800',
                                        'fulfilled' => 'bg-madi-100 text-madi-800',
                                        'paid' => 'bg-dhooni-100 text-dhooni-800',
                                        default => 'bg-moodhu-100 text-muraka-700',
                                    };
                                @endphp
                                <span class="badge mt-1 {{ $statusClass }}">{{ $order->status }}</span>
                            </div>
                        </div>

                        @if($order->isFulfillable() && ! $order->isCompleted())
                            <form method="POST" action="{{ route('provider.orders.fulfill', $order) }}" class="mt-3 flex gap-2">
                                @csrf
                                <input name="voucher_code" type="text" placeholder="Tourist's voucher code" class="input-field flex-1 text-sm" autocomplete="off">
                                <button type="submit" class="btn-secondary shrink-0">Verify & complete</button>
                            </form>
                            @error('voucher_code')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                        @endif
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </section>
</x-marketplace-layout>
