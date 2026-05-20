<x-marketplace-layout>
    <a href="{{ route('orders.index') }}" class="text-sm text-cyan-600 hover:underline">← My orders</a>

    <article class="mt-3 grid lg:grid-cols-5 gap-6">
        <section class="lg:col-span-3 space-y-4">
            <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-6 shadow-xl">
                <div class="text-emerald-100 text-xs uppercase tracking-wider font-medium">
                    @switch($order->status)
                        @case('completed') Voucher redeemed @break
                        @case('fulfilled') Fulfilled @break
                        @case('cancelled') Cancelled @break
                        @case('refunded') Refunded @break
                        @default Show this voucher to the provider
                    @endswitch
                </div>
                <h1 class="text-2xl font-bold mt-1">{{ $order->listing->title }}</h1>
                <p class="text-emerald-50 text-sm">{{ $order->listing->provider->business_name }} · {{ $order->listing->island->name }}</p>

                <div class="mt-6 bg-white/10 backdrop-blur rounded-2xl p-5 text-center">
                    <div class="text-xs text-emerald-100 uppercase tracking-wider mb-2">Voucher code</div>
                    <div class="font-mono text-3xl sm:text-4xl font-bold tracking-widest">{{ $order->voucher_code }}</div>
                    <div class="mt-4 inline-block bg-white rounded-2xl p-4">
                        <div class="text-gray-900 text-xs font-mono">
                            <div class="grid grid-cols-12 gap-px">
                                @php
                                    $hash = md5($order->voucher_code);
                                    $bits = '';
                                    foreach (str_split($hash) as $h) {
                                        $bits .= str_pad(decbin(hexdec($h)), 4, '0', STR_PAD_LEFT);
                                    }
                                @endphp
                                @for($i = 0; $i < 144; $i++)
                                    <div class="aspect-square {{ ($bits[$i % strlen($bits)] === '1') ? 'bg-gray-900' : 'bg-white' }}"></div>
                                @endfor
                            </div>
                        </div>
                        <div class="text-[10px] text-gray-500 mt-1">QR placeholder · scan via provider app</div>
                    </div>
                </div>

                <div class="mt-4 text-xs text-emerald-50 grid grid-cols-2 gap-2">
                    <div><span class="opacity-75">Reference</span><br><strong>{{ $order->reference }}</strong></div>
                    <div><span class="opacity-75">Ordered</span><br><strong>{{ $order->created_at->diffForHumans() }}</strong></div>
                </div>
            </div>

            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-semibold mb-3">Status timeline</h2>
                <ol class="relative border-l-2 border-cyan-200 dark:border-cyan-800 ml-2 space-y-4 pl-4">
                    <li>
                        <span class="absolute -left-2 w-4 h-4 bg-cyan-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        <div class="text-xs text-gray-500">{{ $order->created_at->format('M j, g:i A') }}</div>
                        <div class="font-medium">Order placed</div>
                    </li>
                    <li>
                        <span class="absolute -left-2 w-4 h-4 {{ $order->payment ? 'bg-emerald-500' : 'bg-gray-300' }} rounded-full border-2 border-white dark:border-gray-800"></span>
                        <div class="text-xs text-gray-500">{{ $order->payment?->charged_at?->format('M j, g:i A') ?? '—' }}</div>
                        <div class="font-medium">Paid via BML Swipe {{ $order->payment ? '· '.$order->payment->swipe_reference : '' }}</div>
                    </li>
                    <li>
                        <span class="absolute -left-2 w-4 h-4 {{ $order->fulfilled_at ? 'bg-emerald-500' : 'bg-gray-300' }} rounded-full border-2 border-white dark:border-gray-800"></span>
                        <div class="text-xs text-gray-500">{{ $order->fulfilled_at?->format('M j, g:i A') ?? 'Pending' }}</div>
                        <div class="font-medium">Voucher scanned, fulfilled</div>
                    </li>
                    <li>
                        <span class="absolute -left-2 w-4 h-4 {{ $order->completed_at ? 'bg-emerald-500' : 'bg-gray-300' }} rounded-full border-2 border-white dark:border-gray-800"></span>
                        <div class="text-xs text-gray-500">{{ $order->completed_at?->format('M j, g:i A') ?? 'Pending release' }}</div>
                        <div class="font-medium">Funds released to provider</div>
                    </li>
                </ol>
            </div>
        </section>

        <aside class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-semibold mb-3">Payment</h2>
                <dl class="text-sm space-y-1.5">
                    <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Base</dt><dd>MVR {{ number_format($order->payment?->amount_mvr - $order->payment?->tgst_amount, 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">TGST</dt><dd>MVR {{ number_format($order->payment?->tgst_amount ?? 0, 2) }}</dd></div>
                    <div class="flex justify-between font-semibold border-t pt-2 mt-2"><dt>Total paid</dt><dd>MVR {{ number_format($order->amount_mvr, 2) }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-800 p-4 text-xs">
                <div class="font-semibold mb-1">Provider</div>
                <div>{{ $order->listing->provider->business_name }}</div>
                @if($order->special_requests)
                    <div class="font-semibold mt-3 mb-1">Special requests</div>
                    <div class="text-gray-700 dark:text-gray-300">{{ $order->special_requests }}</div>
                @endif
            </div>
        </aside>
    </article>
</x-marketplace-layout>
