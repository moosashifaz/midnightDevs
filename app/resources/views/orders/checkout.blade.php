<x-marketplace-layout>
    <a href="{{ route('listing', $listing) }}" class="text-sm text-cyan-600 hover:underline">← Back</a>
    <h1 class="text-2xl font-bold mt-2 mb-6">Checkout</h1>

    <div class="grid lg:grid-cols-5 gap-6">
        <form method="POST" action="{{ route('checkout.store', $listing) }}" class="lg:col-span-3 space-y-5">
            @csrf

            <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-semibold mb-3">1. Order summary</h2>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-100 to-blue-100 rounded-xl flex items-center justify-center text-3xl">
                        @switch($listing->category)
                            @case('eat') 🍽️ @break
                            @case('wash') 🧺 @break
                            @case('buy') 🛍️ @break
                            @case('experience') ⛵ @break
                        @endswitch
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold">{{ $listing->title }}</div>
                        <div class="text-xs text-gray-500">{{ $listing->provider->business_name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold">MVR {{ number_format($listing->price_mvr, 2) }}</div>
                        <div class="text-[11px] text-gray-500">≈ USD {{ number_format($listing->price_usd, 2) }}</div>
                    </div>
                </div>
            </section>

            @if($listing->type !== 'instant')
                <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
                    <h2 class="font-semibold mb-3">2. When?</h2>
                    <label class="text-sm text-gray-700 dark:text-gray-300">
                        Pick a date / time
                        <input type="datetime-local" name="scheduled_for" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-2 focus:ring-cyan-500" min="{{ now()->format('Y-m-d\TH:i') }}">
                    </label>
                </section>
            @endif

            <section class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="font-semibold mb-3">{{ $listing->type === 'instant' ? '2' : '3' }}. Special requests <span class="text-xs text-gray-500 font-normal">(optional)</span></h2>
                <textarea name="special_requests" rows="3" placeholder="Allergies, pickup notes, dietary preferences..." class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm focus:ring-2 focus:ring-cyan-500"></textarea>
            </section>

            <section class="rounded-2xl bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/30 dark:to-blue-900/30 border border-cyan-200 dark:border-cyan-800 p-5">
                <h2 class="font-semibold mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    {{ $listing->type === 'instant' ? '3' : '4' }}. Pay via BML Swipe
                </h2>
                <p class="text-sm text-gray-700 dark:text-gray-200">Tap to charge securely via the BML Swipe rail. Funds are held in escrow until you scan your voucher with the provider.</p>
            </section>

            <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold py-4 rounded-2xl shadow-lg shadow-cyan-500/30 text-lg">
                Pay MVR {{ number_format($listing->price_mvr, 2) }} via Swipe
            </button>
        </form>

        <aside class="lg:col-span-2 self-start">
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-5 sticky top-20">
                <h3 class="font-semibold mb-3">Total breakdown</h3>
                <dl class="text-sm space-y-1.5">
                    <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Base price</dt><dd>MVR {{ number_format($listing->price_mvr / 1.16, 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">TGST (16%)</dt><dd>MVR {{ number_format($listing->price_mvr - ($listing->price_mvr / 1.16), 2) }}</dd></div>
                    <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 font-bold"><dt>Total</dt><dd class="text-cyan-700 dark:text-cyan-300">MVR {{ number_format($listing->price_mvr, 2) }}</dd></div>
                    <div class="flex justify-between text-xs text-gray-500"><dt>In USD</dt><dd>≈ ${{ number_format($listing->price_usd, 2) }}</dd></div>
                </dl>
                <p class="text-[11px] text-gray-500 mt-4">FX rate from Central Bank of Maldives reference; updated daily. No platform fee added at checkout.</p>
            </div>
        </aside>
    </div>
</x-marketplace-layout>
