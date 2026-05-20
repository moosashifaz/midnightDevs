<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#5999CF">

        <title>{{ config('app.name', 'AfterArrival') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-moodhu-50 text-muraka-900">
        <livewire:layout.navigation />

        @if (isset($header))
            <header class="bg-white border-b border-moodhu-200">
                <div class="max-w-6xl mx-auto py-5 px-4 sm:px-6">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="max-w-6xl mx-auto px-4 py-8">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
