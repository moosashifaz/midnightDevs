<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="space-y-4">
    <div>
        <p class="section-eyebrow">Account</p>
        <h1 class="section-title mt-1">Reset password</h1>
        <p class="text-sm text-muraka-600 mt-2">Enter your email and we will send you a reset link.</p>
    </div>

    <x-auth-session-status class="mb-2" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-4">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Email reset link') }}
        </x-primary-button>
    </form>

    <x-ui.auth-footer
        alternate-label="Back to log in"
        :alternate-href="route('login')"
    />
</div>
