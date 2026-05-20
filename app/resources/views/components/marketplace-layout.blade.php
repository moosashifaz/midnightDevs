<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0891b2">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>{{ $title ?? 'AfterArrival — Maldives in-stay services' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gradient-to-b from-cyan-50 via-white to-white min-h-screen text-gray-900 dark:bg-gray-900 dark:text-gray-100">

    <header class="sticky top-0 z-40 backdrop-blur bg-white/80 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-bold shadow-md">AA</span>
                <div>
                    <div class="font-bold leading-tight">AfterArrival</div>
                    <div class="text-[10px] uppercase tracking-wider text-gray-500">Maldives · In-stay</div>
                </div>
            </a>

            <nav class="hidden sm:flex items-center gap-2 text-sm">
                @foreach(\App\Models\Listing::CATEGORIES as $key => $label)
                    <a href="{{ route('category', $key) }}" class="px-3 py-1.5 rounded-full hover:bg-cyan-100 dark:hover:bg-cyan-900/30 {{ request()->routeIs('category') && request()->route('category') === $key ? 'bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-300 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-cyan-600 dark:text-gray-300 hidden sm:inline">My Orders</a>
                    @if(auth()->user()->isProvider())
                        <a href="{{ route('provider.dashboard') }}" class="text-gray-600 hover:text-cyan-600 dark:text-gray-300">Provider</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 text-sm">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-cyan-600 dark:text-gray-300">Log in</a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-1.5 rounded-full font-medium shadow hover:shadow-md">Sign up</a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="max-w-6xl mx-auto px-4 mt-3">
            <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif

    <main class="max-w-6xl mx-auto px-4 py-6 pb-20">
        {{ $slot }}
    </main>

    <nav class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-gray-900/95 border-t border-gray-200 dark:border-gray-800 backdrop-blur">
        <div class="grid grid-cols-4 text-xs">
            @foreach(\App\Models\Listing::CATEGORIES as $key => $label)
                <a href="{{ route('category', $key) }}" class="flex flex-col items-center py-2 {{ request()->routeIs('category') && request()->route('category') === $key ? 'text-cyan-600 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
                    <span class="text-base">
                        @switch($key)
                            @case('eat') 🍽️ @break
                            @case('wash') 🧺 @break
                            @case('buy') 🛍️ @break
                            @case('experience') ⛵ @break
                        @endswitch
                    </span>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    @auth
        @livewire('a-i-chat')
    @endauth

    @livewireScripts
</body>
</html>
