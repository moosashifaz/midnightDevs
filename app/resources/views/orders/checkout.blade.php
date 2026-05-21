<x-marketplace-layout>
    <div class="page-wrap">
    <a href="{{ route('listing', $listing) }}" class="inline-flex items-center gap-1 text-sm text-miyaru-700 hover:text-madi-600 font-medium transition-colors duration-150">
        <x-icons.icon name="chevron-left" class="w-4 h-4" /> Back
    </a>
    <x-ui.page-header title="Checkout" class="mt-2" />

    @if(!auth()->check())
        <div class="mb-6">
            <x-ui.card class="p-6 bg-moodhu-50 border-madi-200">
                <h2 class="font-semibold text-lg mb-4 flex items-center gap-2">
                    <x-icons.icon name="user" class="w-5 h-5 text-madi-600" />
                    Quick Authentication
                </h2>
                <p class="text-sm text-muraka-700 mb-4">Enter your email or phone number to continue. We'll send you a verification code to create your account or log you in.</p>
                
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                
                <livewire:otp-auth />
            </x-ui.card>
        </div>
    @else
        <div class="grid lg:grid-cols-5 gap-6">
        <form method="POST" action="{{ route('checkout.store', $listing) }}" class="lg:col-span-3 space-y-5">
            @csrf

            <x-ui.card class="p-5">
                <h2 class="font-semibold mb-3 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-madi-100 text-madi-700 text-xs font-bold">1</span>
                    Order summary
                </h2>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0">
                        <x-listing-image :listing="$listing" class="w-full h-full aspect-square" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-muraka-900">{{ $listing->title }}</div>
                        <div class="text-xs text-muraka-500">{{ $listing->provider->business_name }}</div>
                    </div>
                    <div class="text-right shrink-0">
                        <x-ui.price :usd="$listing->price_usd" :mvr="$listing->price_mvr" size="sm" />
                    </div>
                </div>
            </x-ui.card>

            @if($listing->type !== 'instant')
                <x-ui.card class="p-5">
                    <h2 class="font-semibold mb-3 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-madi-100 text-madi-700 text-xs font-bold">2</span>
                        When?
                    </h2>
                    <label class="text-sm text-muraka-700 block">
                        Pick a date / time
                        <input type="datetime-local" name="scheduled_for" class="input-field mt-1" min="{{ now()->format('Y-m-d\TH:i') }}">
                    </label>
                </x-ui.card>
            @endif

            <x-ui.card class="p-5">
                <h2 class="font-semibold mb-3 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-madi-100 text-madi-700 text-xs font-bold">{{ $listing->type === 'instant' ? '2' : '3' }}</span>
                    Special requests <span class="text-xs text-muraka-500 font-normal">(optional)</span>
                </h2>
                <textarea name="special_requests" rows="3" placeholder="Allergies, pickup notes, dietary preferences..." class="input-field text-sm"></textarea>
            </x-ui.card>

            <x-ui.card class="p-5 bg-moodhu-50 border-moodhu-200">
                <h2 class="font-semibold mb-3 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-madi-100 text-madi-700 text-xs font-bold">{{ $listing->type === 'instant' ? '3' : '4' }}</span>
                    <x-icons.icon name="credit-card" class="w-5 h-5 text-madi-600" />
                    Pay via BML Swipe
                </h2>
                <p class="text-sm text-muraka-700">Tap to charge securely via BML Swipe. Funds held in escrow until you scan your voucher with the provider.</p>
            </x-ui.card>

            <button type="submit" class="btn-primary w-full py-4 text-lg">
                Pay ${{ number_format($listing->price_usd, 2) }} via Swipe
            </button>
            <p class="text-center text-[11px] text-muraka-500 -mt-2">Charged ≈ MVR {{ number_format($listing->price_mvr, 0) }} on BML Swipe</p>
        </form>

        <aside class="lg:col-span-2 self-start">
            <x-ui.card class="p-5 sticky top-[calc(var(--site-header-height)+1rem)]">
                <h3 class="font-semibold mb-3">Total breakdown</h3>
                <dl class="text-sm space-y-1.5">
                    <div class="flex justify-between"><dt class="text-muraka-500">Base price</dt><dd>${{ number_format($listing->price_usd / 1.16, 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-muraka-500">TGST (16%)</dt><dd>${{ number_format($listing->price_usd - ($listing->price_usd / 1.16), 2) }}</dd></div>
                    <div class="flex justify-between border-t border-moodhu-200 pt-2 mt-2 font-bold"><dt>Total</dt><dd class="text-madi-700">${{ number_format($listing->price_usd, 2) }}</dd></div>
                    <div class="flex justify-between text-xs text-muraka-500"><dt>Swipe charge</dt><dd>≈ MVR {{ number_format($listing->price_mvr, 0) }}</dd></div>
                </dl>
                <p class="text-[11px] text-muraka-500 mt-4">USD estimate for travelers. Settlement is in MVR via BML Swipe.</p>
            </x-ui.card>
        </aside>
    </div>
    @endif
    </div>
</x-marketplace-layout>
