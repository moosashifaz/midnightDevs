<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
}; ?>

<div class="space-y-4">
    <div>
        <p class="section-eyebrow">Get started</p>
        <h1 class="section-title mt-1">Sign up with OTP</h1>
        <p class="text-sm text-muraka-600 mt-2">Create your account instantly using your phone number or email. No password needed.</p>
    </div>

    <livewire:otp-auth />

    <x-ui.auth-footer
        alternate-label="Already have an account? Log in"
        :alternate-href="route('login')"
    />
</div>
