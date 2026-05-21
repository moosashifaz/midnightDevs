<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#5999CF">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>{{ $title ?? 'AfterArrival — Maldives in-stay services' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-moodhu-50 min-h-screen text-muraka-900">

    <header class="site-header sticky top-0 z-40 backdrop-blur bg-white/90 border-b border-moodhu-200">
        <div class="site-header-inner w-full px-6 sm:px-10 lg:px-16 py-2 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center" aria-label="AfterArrival home">
                <x-ui.brand-logo :size="144" class="header-brand-lottie" />
            </a>

            <nav class="hidden sm:flex items-center gap-1 text-sm">
                @foreach(\App\Models\Listing::CATEGORIES as $key => $label)
                    <a href="{{ route('category', $key) }}"
                       class="{{ request()->routeIs('category') && request()->route('category') === $key ? 'nav-pill-active' : 'nav-pill' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('plans.index') }}" class="btn-ghost hidden sm:inline-flex py-1.5">My plans</a>
                    <a href="{{ route('orders.index') }}" class="btn-ghost hidden sm:inline-flex py-1.5">My Orders</a>
                    @if(auth()->user()->isProvider())
                        <a href="{{ route('provider.dashboard') }}" class="btn-ghost py-1.5">Provider</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-muraka-500 hover:text-red-600 text-sm transition-colors duration-150">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost py-1.5">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary py-1.5 px-4">Sign up</a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="w-full px-6 sm:px-10 lg:px-16 mt-3">
            <div class="rounded-xl bg-ruh-50 border border-ruh-200 text-ruh-900 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif

    <main class="w-full pb-24 sm:pb-6">
        {{ $slot }}
    </main>

    <nav class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 border-t border-moodhu-200 backdrop-blur">
        <div class="grid grid-cols-4 text-xs w-full">
            @foreach(\App\Models\Listing::CATEGORIES as $key => $label)
                @php $active = request()->routeIs('category') && request()->route('category') === $key; @endphp
                <a href="{{ route('category', $key) }}"
                   class="flex flex-col items-center py-2 border-t-2 transition-colors duration-150 {{ $active ? 'border-madi-500 text-madi-600 font-medium' : 'border-transparent text-muraka-500' }}">
                    <x-icons.category :category="$key" class="w-5 h-5 {{ $active ? 'text-madi-600' : '' }}" />
                    <span class="mt-0.5">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @auth
        @livewire('a-i-chat')
    @endauth

    <x-ui.site-footer />

    @livewireScripts
</body>
</html>
