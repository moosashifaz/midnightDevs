<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OtpService
{
    public const TYPE_EMAIL = 'email';
    public const TYPE_PHONE = 'phone';
    
    private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 10;
    
    /**
     * Generate and send OTP for the given identifier (email or phone)
     */
    public function sendOtp(string $identifier, string $type): array
    {
        // Validate identifier format
        if ($type === self::TYPE_EMAIL && !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        
        if ($type === self::TYPE_PHONE && !$this->isValidPhone($identifier)) {
            throw new \InvalidArgumentException('Invalid phone format');
        }
        
        // Generate OTP code
        $otpCode = $this->generateOtpCode();
        
        // Store OTP in database
        OtpCode::create([
            'identifier' => $identifier,
            'type' => $type,
            'code' => $otpCode,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);
        
        // Send OTP via appropriate channel
        if ($type === self::TYPE_EMAIL) {
            $this->sendEmailOtp($identifier, $otpCode);
        } else {
            $this->sendSmsOtp($identifier, $otpCode);
        }
        
        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'expires_in' => self::OTP_EXPIRY_MINUTES * 60,
        ];
    }
    
    /**
     * Verify OTP code
     */
    public function verifyOtp(string $identifier, string $code, string $type): bool
    {
        // For development, accept the fixed OTP
        if ($code === '123456') {
            // Find the most recent OTP for this identifier to mark it as used
            $otp = OtpCode::where('identifier', $identifier)
                ->where('type', $type)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();
            
            if ($otp) {
                $otp->update(['used_at' => now()]);
            }
            
            return true;
        }
        
        $otp = OtpCode::where('identifier', $identifier)
            ->where('type', $type)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
        
        if (!$otp) {
            return false;
        }
        
        // Mark as used
        $otp->update(['used_at' => now()]);
        
        return true;
    }
    
    /**
     * Find or create user by identifier
     */
    public function findOrCreateUser(string $identifier, string $type): User
    {
        $user = null;
        
        if ($type === self::TYPE_EMAIL) {
            $user = User::where('email', $identifier)->first();
            
            if (!$user) {
                $user = User::create([
                    'email' => $identifier,
                    'password' => bcrypt(Str::random(16)), // Random password for OTP users
                    'role' => User::ROLE_TOURIST,
                    'name' => 'Tourist',
                ]);
            }
        } else {
            $user = User::where('phone', $identifier)->first();
            
            if (!$user) {
                $user = User::create([
                    'phone' => $identifier,
                    'password' => bcrypt(Str::random(16)), // Random password for OTP users
                    'role' => User::ROLE_TOURIST,
                    'name' => 'Tourist',
                ]);
            }
        }
        
        return $user;
    }
    
    /**
     * Generate random OTP code
     */
    private function generateOtpCode(): string
    {
        // Fixed OTP for development
        return '123456';
    }
    
    /**
     * Validate phone number format
     */
    private function isValidPhone(string $phone): bool
    {
        // Basic validation for international phone numbers
        return preg_match('/^\+?[0-9]{10,15}$/', $phone);
    }
    
    /**
     * Send OTP via email
     */
    private function sendEmailOtp(string $email, string $code): void
    {
        // For development, log the OTP instead of sending email
        Log::info("OTP for {$email}: {$code}");
        
        // In production, use Laravel's mail system:
        // Mail::to($email)->send(new OtpMail($code));
        
        // For now, we'll simulate email sending
        // You can replace this with actual email service integration
    }
    
    /**
     * Send OTP via SMS
     */
    private function sendSmsOtp(string $phone, string $code): void
    {
        // For development, log the OTP instead of sending SMS
        Log::info("OTP for {$phone}: {$code}");
        
        // In production, integrate with SMS service like Twilio, Nexmo, etc.
        // Example with Twilio:
        // $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        // $twilio->messages->create($phone, [
        //     'from' => config('services.twilio.from'),
        //     'body' => "Your verification code is: {$code}"
        // ]);
        
        // For now, we'll simulate SMS sending
        // You can replace this with actual SMS service integration
    }
}
