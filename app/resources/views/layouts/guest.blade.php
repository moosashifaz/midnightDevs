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
    <body class="font-sans text-muraka-900 antialiased bg-moodhu-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div>
                <a href="{{ route('home') }}" wire:navigate class="flex flex-col items-center gap-2">
                    <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-madi-500 text-white shadow-card">
                        <x-icons.icon name="waves" class="w-8 h-8" />
                    </span>
                    <span class="font-bold text-muraka-900">AfterArrival</span>
                    <span class="text-[10px] uppercase tracking-label text-muraka-500">Maldives · In-stay</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 card">
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>
