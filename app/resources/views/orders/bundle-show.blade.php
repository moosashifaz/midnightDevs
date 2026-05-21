<x-marketplace-layout>
    <div class="page-wrap">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1 text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150">
            <x-icons.icon name="chevron-left" class="w-4 h-4" /> My orders
        </a>

        @if(session('status'))
            <div class="mt-4 rounded-xl border border-madi-200 bg-madi-50 px-4 py-3 text-sm text-madi-900">
                {{ session('status') }}
            </div>
        @endif

        <article class="mt-4">
            <p class="section-eyebrow mb-1">Plan bundle</p>
            <h1 class="section-title mb-1">Booked {{ $bundle->item_count }} activities</h1>
            <p class="text-sm text-muraka-600 mb-6">
                Reference <strong>{{ $bundle->reference }}</strong>
                · Paid in one checkout
                @if($bundle->payment?->swipe_reference)
                    · Swipe {{ $bundle->payment->swipe_reference }}
                @endif
            </p>

            <div class="card p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-label text-madi-600">Bundle total</p>
                    <x-ui.price :usd="$bundle->amount_usd" :mvr="$bundle->amount_mvr" size="lg" />
                </div>
                <span class="badge bg-dhooni-100 text-dhooni-800">{{ $bundle->status }}</span>
            </div>

            <h2 class="font-semibold text-muraka-900 mb-3">Your vouchers — one per activity</h2>
            <div class="space-y-4">
                @foreach($bundle->orders as $order)
                    <x-ui.card class="p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-semibold uppercase tracking-label text-madi-600">Voucher</p>
                                <p class="font-mono text-2xl font-bold tracking-widest text-muraka-900">{{ $order->voucher_code }}</p>
                                <p class="font-semibold text-muraka-900 mt-2">{{ $order->listing->title }}</p>
                                <p class="text-sm text-muraka-500">{{ $order->listing->provider->business_name }} · {{ $order->listing->island->name ?? 'Island' }}</p>
                                <p class="text-xs text-muraka-400 mt-1">Order {{ $order->reference }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <x-ui.price :usd="$order->amount_usd" :mvr="$order->amount_mvr" size="sm" />
                                <a href="{{ route('orders.show', $order) }}" class="mt-2 inline-block text-xs font-semibold text-madi-700 hover:text-madi-800">
                                    View details →
                                </a>
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        </article>
    </div>
</x-marketplace-layout>
