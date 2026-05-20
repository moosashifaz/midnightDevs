<x-app-layout>
    <x-slot name="header">
        <h2 class="section-title">{{ __('Dashboard') }}</h2>
    </x-slot>

    <x-ui.card class="p-6">
        <p class="text-muraka-700">{{ __("You're logged in!") }}</p>
        <a href="{{ route('home') }}" class="inline-block mt-4 text-madi-600 hover:text-madi-700 font-medium text-sm transition-colors duration-150">Go to marketplace →</a>
    </x-ui.card>
</x-app-layout>
