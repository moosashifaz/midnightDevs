<footer class="border-t border-moodhu-200 bg-white text-muraka-700">
    <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 py-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <div class="space-y-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-lg font-semibold text-muraka-900">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-madi-500 text-white shadow-card">
                    <x-icons.icon name="waves" class="w-5 h-5" />
                </span>
                AfterArrival
            </a>
            <p class="text-sm text-muraka-500 max-w-sm">
                Local in-stay services for Maldives guests: food, wellness, island experiences, and handpicked essentials.
            </p>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-muraka-900 uppercase tracking-[0.18em] mb-4">Categories</h3>
            <ul class="space-y-3 text-sm text-muraka-600">
                @foreach(\App\Models\Listing::CATEGORIES as $key => $label)
                    <li><a href="{{ route('category', $key) }}" class="hover:text-madi-600 transition-colors">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-muraka-900 uppercase tracking-[0.18em] mb-4">Explore</h3>
            <ul class="space-y-3 text-sm text-muraka-600">
                <li><a href="{{ route('home') }}" class="hover:text-madi-600 transition-colors">Home</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-madi-600 transition-colors">Log in</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-madi-600 transition-colors">Sign up</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-muraka-900 uppercase tracking-[0.18em] mb-4">About</h3>
            <p class="text-sm text-muraka-500 mb-4">AfterArrival brings authentic island services and local culture to your stay with a simple, island-ready experience.</p>
            <div class="flex items-center gap-3 text-muraka-500 text-sm">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-muraka-100">A</span>
                <div>
                    <p class="font-medium text-muraka-900">AfterArrival</p>
                    <p class="text-xs">Maldives in-stay marketplace</p>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-moodhu-200">
        <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 py-4 text-xs text-muraka-500 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} AfterArrival. Made for island stays in the Maldives.</p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('home') }}" class="hover:text-madi-600 transition-colors">Privacy</a>
                <a href="{{ route('home') }}" class="hover:text-madi-600 transition-colors">Terms</a>
            </div>
        </div>
    </div>
</footer>
