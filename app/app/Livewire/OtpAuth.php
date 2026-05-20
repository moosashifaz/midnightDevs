<?php

namespace App\Livewire;

use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class OtpAuth extends Component
{
    #[Validate('required|string')]
    public string $identifier = '';

    #[Validate('required|in:email,phone')]
    public string $type = 'email';

    #[Validate('required|string|digits:6')]
    public string $otp = '';

    public bool $otpSent = false;
    public bool $loading = false;
    public string $message = '';
    public string $messageType = '';

    public function sendOtp(OtpService $otpService): void
    {
        $this->validate([
            'identifier' => 'required|string',
            'type' => 'required|in:email,phone',
        ]);

        $this->loading = true;

        try {
            $result = $otpService->sendOtp($this->identifier, $this->type);
            
            // Store the current URL as intended URL for redirect after authentication
            if (!session()->has('url.intended')) {
                session()->put('url.intended', request()->url());
            }
            
            $this->otpSent = true;
            $this->message = 'OTP sent successfully! Please check your ' . $this->type;
            $this->messageType = 'success';
        } catch (\InvalidArgumentException $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        } catch (\Exception $e) {
            $this->message = 'Failed to send OTP. Please try again.';
            $this->messageType = 'error';
        }

        $this->loading = false;
    }

    public function verifyOtp(OtpService $otpService): void
    {
        $this->validate([
            'identifier' => 'required|string',
            'type' => 'required|in:email,phone',
            'otp' => 'required|string|digits:6',
        ]);

        $this->loading = true;

        try {
            $isValid = $otpService->verifyOtp($this->identifier, $this->otp, $this->type);

            if (!$isValid) {
                $this->message = 'Invalid or expired OTP. Please try again.';
                $this->messageType = 'error';
                $this->loading = false;
                return;
            }

            // Find or create user
            $user = $otpService->findOrCreateUser($this->identifier, $this->type);

            // Login the user
            Auth::login($user);

            // Redirect to intended URL or home
            $this->redirect(session()->pull('url.intended', route('home')));
        } catch (\Illuminate\Database\QueryException $e) {
            $this->message = 'Database error: ' . $e->getMessage();
            $this->messageType = 'error';
        } catch (\Exception $e) {
            $this->message = 'Authentication failed: ' . $e->getMessage();
            $this->messageType = 'error';
        }

        $this->loading = false;
    }

    public function resendOtp(OtpService $otpService): void
    {
        $this->otp = '';
        $this->sendOtp($otpService);
    }

    public function resetForm(): void
    {
        $this->identifier = '';
        $this->type = 'email';
        $this->otp = '';
        $this->otpSent = false;
        $this->message = '';
        $this->messageType = '';
    }
}
