<section class="mb-10" aria-labelledby="why-afterarrival-heading">
    <div class="mb-6 max-w-2xl">
        <p class="section-eyebrow">Why AfterArrival</p>
        <h2 id="why-afterarrival-heading" class="section-title mt-1">The easier way to book on your island</h2>
        <p class="text-sm text-muraka-600 mt-2 leading-relaxed">
            Local services, safe digital payments, and clear pickup — built for tourists staying in the Maldives.
        </p>
    </div>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-ui.value-card
            step="1"
            accent="madi"
            accent-position="top-left"
            title="Verified island providers"
            description="Every listing is from a real business on your island — food, laundry, crafts, and tours in one place."
            :interactive="false"
        >
            <x-slot:icon><x-icons.icon name="shield-check" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>

        <x-ui.value-card
            step="2"
            accent="iru"
            accent-position="bottom-right"
            title="Pay safely with BML Swipe"
            description="Funds are held until you scan your voucher — not released until your order is fulfilled."
            :interactive="false"
        >
            <x-slot:icon><x-icons.icon name="credit-card" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>

        <x-ui.value-card
            step="3"
            accent="ruh"
            accent-position="top-right"
            title="Book in minutes"
            description="Order dinner, laundry, souvenirs, or a snorkel trip without chasing messages or cash."
            :interactive="false"
        >
            <x-slot:icon><x-icons.icon name="clock" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>

        <x-ui.value-card
            step="4"
            accent="miyaru"
            accent-position="bottom-left"
            title="Simple QR redemption"
            description="Get a digital voucher instantly. Show it to the provider — no confusion at handover."
            :interactive="false"
        >
            <x-slot:icon><x-icons.icon name="qr-code" class="w-5 h-5" /></x-slot:icon>
        </x-ui.value-card>
    </div>
</section>
