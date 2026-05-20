<section
    class="mb-10"
    aria-labelledby="why-afterarrival-heading"
    x-data="{
        active: 1,
        steps: {
            1: { label: 'Browse', detail: 'Explore Eat, Wash, Buy, and Experience from providers verified on your island — no random WhatsApp numbers.' },
            2: { label: 'Pay', detail: 'Checkout with BML Swipe. Your payment stays in escrow until you redeem your order.' },
            3: { label: 'Scan', detail: 'Show your QR voucher at pickup. The provider scans it to confirm fulfillment.' },
            4: { label: 'Review', detail: 'Funds release to the provider after completion. Leave a review from a real completed order.' },
        },
        setActive(n) { this.active = n },
    }"
>
    <div class="mb-6 max-w-2xl">
        <p class="section-eyebrow">Why AfterArrival</p>
        <h2 id="why-afterarrival-heading" class="section-title mt-1">The easier way to book on your island</h2>
        <p class="text-sm text-muraka-600 mt-2 leading-relaxed">
            Local services, safe digital payments, and clear pickup — built for tourists staying in the Maldives.
        </p>
    </div>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
        <x-ui.value-card
            step="1"
            accent="madi"
            accent-position="top-left"
            title="Verified island providers"
            description="Every listing is from a real business on your island — food, laundry, crafts, and tours in one place."
            @click="setActive(1)"
            x-bind:class="active === 1 ? 'ring-2 ring-madi-400 ring-offset-2 shadow-card-hover' : ''"
        >
            <x-slot:icon><x-icons.icon name="shield-check" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>

        <x-ui.value-card
            step="2"
            accent="iru"
            accent-position="bottom-right"
            title="Pay safely with BML Swipe"
            description="Funds are held until you scan your voucher — not released until your order is fulfilled."
            @click="setActive(2)"
            x-bind:class="active === 2 ? 'ring-2 ring-iru-400 ring-offset-2 shadow-card-hover' : ''"
        >
            <x-slot:icon><x-icons.icon name="credit-card" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>

        <x-ui.value-card
            step="3"
            accent="ruh"
            accent-position="top-right"
            title="Book in minutes"
            description="Order dinner, laundry, souvenirs, or a snorkel trip without chasing messages or cash."
            @click="setActive(3)"
            x-bind:class="active === 3 ? 'ring-2 ring-ruh-400 ring-offset-2 shadow-card-hover' : ''"
        >
            <x-slot:icon><x-icons.icon name="clock" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>

        <x-ui.value-card
            step="4"
            accent="miyaru"
            accent-position="bottom-left"
            title="Simple QR redemption"
            description="Get a digital voucher instantly. Show it to the provider — no confusion at handover."
            @click="setActive(4)"
            x-bind:class="active === 4 ? 'ring-2 ring-miyaru-400 ring-offset-2 shadow-card-hover' : ''"
        >
            <x-slot:icon><x-icons.icon name="qr-code" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>
    </div>

    <div class="rounded-3xl border border-moodhu-200 bg-white p-5 sm:p-6 shadow-card">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="text-xs font-semibold uppercase tracking-label text-muraka-500">How it works</span>
            <button type="button" @click="setActive(1)" class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-150" :class="active === 1 ? 'bg-madi-500 text-white' : 'bg-moodhu-100 text-muraka-600 hover:bg-moodhu-200'">Browse</button>
            <button type="button" @click="setActive(2)" class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-150" :class="active === 2 ? 'bg-madi-500 text-white' : 'bg-moodhu-100 text-muraka-600 hover:bg-moodhu-200'">Pay</button>
            <button type="button" @click="setActive(3)" class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-150" :class="active === 3 ? 'bg-madi-500 text-white' : 'bg-moodhu-100 text-muraka-600 hover:bg-moodhu-200'">Scan</button>
            <button type="button" @click="setActive(4)" class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-150" :class="active === 4 ? 'bg-madi-500 text-white' : 'bg-moodhu-100 text-muraka-600 hover:bg-moodhu-200'">Review</button>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
            <div class="flex items-center gap-3 shrink-0">
                <span
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-madi-100 text-lg font-bold text-madi-700"
                    x-text="active"
                ></span>
                <div>
                    <p class="font-bold text-muraka-900">
                        Step <span x-text="active"></span>:
                        <span x-text="steps[active].label"></span>
                    </p>
                    <p class="text-xs text-muraka-500">Select a card or step above</p>
                </div>
            </div>
            <p class="text-sm text-muraka-700 leading-relaxed sm:border-l sm:border-moodhu-200 sm:pl-4 sm:min-h-[3rem]" x-text="steps[active].detail"></p>
        </div>

        <div class="mt-5 flex flex-wrap gap-3">
            @guest
                <x-ui.button variant="primary" :href="route('register')">Get started free</x-ui.button>
                <x-ui.button variant="ghost" :href="route('category', 'eat')">Browse listings</x-ui.button>
            @else
                <x-ui.button variant="primary" :href="route('category', 'experience')">Browse experiences</x-ui.button>
                <x-ui.button variant="ghost" :href="route('orders.index')">My orders</x-ui.button>
            @endguest
        </div>
    </div>
</section>
