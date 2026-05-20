<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur border-b border-moodhu-200 sticky top-0 z-40">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-14">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-madi-500 text-white">
                        <x-icons.icon name="waves" class="w-4 h-4" />
                    </span>
                    <span class="font-bold text-sm text-muraka-900 hidden sm:inline">AfterArrival</span>
                </a>

                <div class="hidden sm:flex items-center gap-1 text-sm">
                    <a href="{{ route('home') }}" wire:navigate class="nav-pill">Marketplace</a>
                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" wire:navigate>Orders</x-nav-link>
                    <x-nav-link :href="route('profile')" :active="request()->routeIs('profile')" wire:navigate>Profile</x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="btn-ghost py-1.5 text-sm">
                            <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                            <x-icons.icon name="chevron-right" class="w-4 h-4 rotate-90" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>{{ __('Profile') }}</x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>{{ __('Log Out') }}</x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="btn-ghost p-2">
                    <x-icons.icon name="x" class="w-5 h-5" x-show="open" style="display: none" />
                    <svg x-show="!open" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-moodhu-200 bg-white">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('home')" wire:navigate>Marketplace</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')" wire:navigate>Orders</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile')" :active="request()->routeIs('profile')" wire:navigate>Profile</x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-3 border-t border-moodhu-200 px-4">
            <div class="font-medium text-muraka-900" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"></div>
            <div class="text-sm text-muraka-500">{{ auth()->user()->email }}</div>
            <button wire:click="logout" class="mt-3 w-full text-start text-sm text-muraka-600">Log Out</button>
        </div>
    </div>
</nav>
