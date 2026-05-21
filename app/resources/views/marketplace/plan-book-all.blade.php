<x-marketplace-layout :title="'Book your plan — AfterArrival'">
    <div class="page-wrap">
        <a href="{{ $returnUrl }}" class="inline-flex items-center gap-1 text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150 mb-4">
            <x-icons.icon name="chevron-right" class="w-4 h-4 rotate-180" />
            Back to plan
        </a>

        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <x-ui.page-header title="Book all in one checkout" class="mt-2" />

        @if($islandName)
            <p class="text-sm text-muraka-600 -mt-2 mb-2">{{ $islandName }} · {{ $availableCount }} of {{ $itemCount }} activities available</p>
        @endif

        @if($summary)
            <p class="text-sm text-muraka-700 mb-6 leading-relaxed max-w-2xl">{{ $summary }}</p>
        @endif

        @auth
            <div class="grid lg:grid-cols-5 gap-6">
                <form method="POST" action="{{ route('plan.book-all.store') }}" class="lg:col-span-3 space-y-5">
                    @csrf

                    <x-ui.card class="p-5">
                        <h2 class="font-semibold mb-3 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-madi-100 text-madi-700 text-xs font-bold">1</span>
                            Your plan ({{ $itemCount }} {{ $itemCount === 1 ? 'item' : 'items' }})
                        </h2>
                        <ul class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            @foreach($lineItems as $index => $row)
                                @php $listing = $row['listing'] ?? null; @endphp
                                <li class="flex items-center gap-3 rounded-lg border border-moodhu-200 bg-moodhu-50/50 px-3 py-2 text-sm">
                                    <span class="text-[10px] font-bold text-madi-600 w-5">{{ $index + 1 }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-muraka-900 line-clamp-1">{{ $row['title'] }}</p>
                                        @if(! empty($row['day']))
                                            <p class="text-[10px] text-muraka-500">Day {{ $row['day'] }}@if(! empty($row['note'])) · {{ $row['note'] }}@endif</p>
                                        @endif
                                        @unless($row['available'] ?? false)
                                            <p class="text-[10px] text-red-600">Unavailable — remove from plan</p>
                                        @endunless
                                    </div>
                                    @if($listing)
                                        <x-ui.price :usd="$row['price_usd']" :mvr="$row['price_mvr']" size="sm" :stacked="false" />
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </x-ui.card>

                    <x-ui.card class="p-5">
                        <h2 class="font-semibold mb-3 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-madi-100 text-madi-700 text-xs font-bold">2</span>
                            Trip notes <span class="text-xs text-muraka-500 font-normal">(optional)</span>
                        </h2>
                        <label class="text-sm text-muraka-700 block">
                            Applies to the whole booking
                            <textarea name="special_requests" rows="3" placeholder="Allergies, pickup notes, preferred times…" class="input-field text-sm mt-1">{{ old('special_requests') }}</textarea>
                        </label>
                        <label class="text-sm text-muraka-700 block mt-3">
                            Preferred start (optional)
                            <input type="datetime-local" name="scheduled_for" value="{{ old('scheduled_for') }}" class="input-field mt-1" min="{{ now()->format('Y-m-d\TH:i') }}">
                        </label>
                    </x-ui.card>

                    <x-ui.card class="p-5 bg-moodhu-50 border-moodhu-200">
                        <h2 class="font-semibold mb-2 flex items-center gap-2">
                            <x-icons.icon name="credit-card" class="w-5 h-5 text-madi-600" />
                            One payment via BML Swipe
                        </h2>
                        <p class="text-sm text-muraka-700">
                            Pay once for the full plan. We create {{ $availableCount }} order{{ $availableCount === 1 ? '' : 's' }} — each activity gets its own QR voucher in My Orders.
                        </p>
                    </x-ui.card>

                    <button type="submit" class="btn-primary w-full py-4 text-lg" @disabled($availableCount === 0)>
                        Pay ${{ number_format($totalUsd, $totalUsd >= 100 ? 0 : 2) }} for all · one checkout
                    </button>
                    <p class="text-center text-[11px] text-muraka-500 -mt-2">Charged ≈ MVR {{ number_format($totalMvr, 0) }} on BML Swipe</p>
                </form>

                <aside class="lg:col-span-2 self-start">
                    <x-ui.card class="p-5 sticky top-[calc(var(--site-header-height)+1rem)]">
                        <h3 class="font-semibold mb-3">Bundle total</h3>
                        <dl class="text-sm space-y-1.5">
                            <div class="flex justify-between"><dt class="text-muraka-500">Activities</dt><dd>{{ $availableCount }}</dd></div>
                            <div class="flex justify-between border-t border-moodhu-200 pt-2 mt-2 font-bold">
                                <dt>Total (USD)</dt>
                                <dd class="text-madi-700">${{ number_format($totalUsd, $totalUsd >= 100 ? 0 : 2) }}</dd>
                            </div>
                            <div class="flex justify-between text-xs text-muraka-500">
                                <dt>Swipe charge</dt>
                                <dd>≈ MVR {{ number_format($totalMvr, 0) }}</dd>
                            </div>
                        </dl>
                        <p class="text-[11px] text-muraka-500 mt-4">Single escrow hold, then release per provider when you redeem each voucher.</p>
                    </x-ui.card>
                </aside>
            </div>
        @else
            <x-ui.card class="p-6 text-center">
                <p class="text-muraka-700 mb-4">Log in to book your full plan in one checkout.</p>
                <x-ui.button :href="route('login')">Log in</x-ui.button>
            </x-ui.card>
        @endauth
    </div>
</x-marketplace-layout>
