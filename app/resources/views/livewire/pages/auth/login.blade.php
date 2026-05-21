<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
}; ?>

<div class="space-y-4">
    <div>
        <p class="section-eyebrow">Account</p>
        <h1 class="section-title mt-1">Sign in with OTP</h1>
        <p class="text-sm text-muraka-600 mt-2">Use your phone number or email to receive a one-time code and log in instantly.</p>
    </div>

    <x-auth-session-status class="mb-2" :status="session('status')" />

    <livewire:otp-auth />

    <x-ui.auth-footer
        alternate-label="Create an account"
        :alternate-href="route('register')"
    />
</div>
