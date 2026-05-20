<div>
    @if(!$otpSent)
        <div class="space-y-4">
            <div class="flex gap-2 mb-4">
                <button 
                    type="button" 
                    wire:click="$set('type', 'email')"
                    class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-colors {{ $type === 'email' ? 'bg-madi-600 text-white' : 'bg-white border border-muraka-300 text-muraka-700 hover:bg-muraka-50' }}"
                >
                    Email
                </button>
                <button 
                    type="button" 
                    wire:click="$set('type', 'phone')"
                    class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-colors {{ $type === 'phone' ? 'bg-madi-600 text-white' : 'bg-white border border-muraka-300 text-muraka-700 hover:bg-muraka-50' }}"
                >
                    Phone
                </button>
            </div>

            <div>
                <label class="block text-sm font-medium text-muraka-700 mb-1">
                    {{ $type === 'email' ? 'Email Address' : 'Phone Number' }}
                </label>
                <input 
                    type="{{ $type === 'email' ? 'email' : 'tel' }}"
                    wire:model="identifier"
                    placeholder="{{ $type === 'email' ? 'you@example.com' : '+960 XXXXXXX' }}"
                    class="input-field w-full"
                    :disabled="$loading"
                />
                @error('identifier') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <button 
                wire:click="sendOtp"
                :disabled="$loading"
                class="btn-primary w-full py-3 {{ $loading ? 'opacity-50 cursor-not-allowed' : '' }}"
            >
                {{ $loading ? 'Sending...' : 'Send Verification Code' }}
            </button>
        </div>
    @else
        <div class="space-y-4">
            <div class="bg-madi-50 border border-madi-200 rounded-lg p-4">
                <p class="text-sm text-madi-800">
                    <x-icons.icon name="check-circle" class="w-4 h-4 inline mr-1" />
                    We've sent a 6-digit code to <strong>{{ $identifier }}</strong>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-muraka-700 mb-1">Enter Verification Code</label>
                <input 
                    type="text"
                    wire:model="otp"
                    placeholder="123456"
                    maxlength="6"
                    class="input-field w-full text-center text-2xl tracking-widest"
                    :disabled="$loading"
                />
                @error('otp') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <button 
                wire:click="verifyOtp"
                :disabled="$loading"
                class="btn-primary w-full py-3 {{ $loading ? 'opacity-50 cursor-not-allowed' : '' }}"
            >
                {{ $loading ? 'Verifying...' : 'Verify & Continue' }}
            </button>

            <div class="text-center">
                <button 
                    wire:click="resendOtp"
                    :disabled="$loading"
                    class="text-sm text-madi-600 hover:text-madi-700 font-medium {{ $loading ? 'opacity-50 cursor-not-allowed' : '' }}"
                >
                    Resend Code
                </button>
                <span class="text-muraka-400 mx-2">|</span>
                <button 
                    wire:click="resetForm"
                    :disabled="$loading"
                    class="text-sm text-muraka-600 hover:text-muraka-700 font-medium {{ $loading ? 'opacity-50 cursor-not-allowed' : '' }}"
                >
                    Change {{ $type === 'email' ? 'Email' : 'Phone' }}
                </button>
            </div>
        </div>
    @endif

    @if($message)
        <div class="mt-4 p-3 rounded-lg text-sm {{ $messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' }}">
            {{ $message }}
        </div>
    @endif
</div>
