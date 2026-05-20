<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="space-y-4">
    <div>
        <p class="section-eyebrow">Account</p>
        <h1 class="section-title mt-1">Verify your email</h1>
        <p class="text-sm text-muraka-600 mt-2">
            Thanks for signing up. Click the link in your email to verify your address. We can send another if needed.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="rounded-xl bg-ruh-50 border border-ruh-200 px-4 py-3 text-sm text-ruh-900">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3">
        <x-primary-button wire:click="sendVerification" class="w-full sm:w-auto justify-center py-3">
            {{ __('Resend verification email') }}
        </x-primary-button>

        <button wire:click="logout" type="button" class="btn-ghost w-full sm:w-auto justify-center py-3">
            {{ __('Log out') }}
        </button>
    </div>

    <x-ui.auth-footer />
</div>
