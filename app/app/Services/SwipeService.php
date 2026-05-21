<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Thin wrapper around the BML Swipe Merchants API.
 *
 * In hackathon-mock mode (config('services.swipe.mock') === true) this returns
 * deterministic fake responses so the full commerce loop can be demoed without
 * real OAuth credentials. When mock mode is off it speaks to either the local
 * mock binary (`swipe mock start`) or the development environment endpoint.
 *
 * Spec: https://github.com/BML-Digital/swipe-merchants-dev
 */
class SwipeService
{
    public function __construct(
        protected string $baseUrl,
        protected ?string $clientId,
        protected ?string $clientSecret,
        protected bool $mock = true,
    ) {
    }

    public function createCharge(Order $order): Payment
    {
        $amount = (float) $order->amount_mvr;
        $tgst = round($amount * 0.16 / 1.16, 2);
        $commissionRate = $this->commissionRate($order->listing->category ?? 'eat');
        $commission = round(($amount - $tgst) * $commissionRate, 2);
        $providerNet = round($amount - $tgst - $commission, 2);

        if ($this->mock) {
            $response = $this->mockChargeResponse($order, $amount);
        } else {
            $response = $this->liveCharge($order, $amount);
        }

        // Normalize status from provider (e.g. "PENDING"/"COMPLETED")
        $respStatus = strtolower($response['status'] ?? 'pending');
        $savedStatus = $respStatus === 'completed' ? Payment::STATUS_COMPLETED : Payment::STATUS_PENDING;

        return Payment::create([
            'order_id' => $order->id,
            'swipe_transaction_id' => $response['id'] ?? null,
            'swipe_reference' => $response['reference'] ?? null,
            'swipe_short_code' => $response['short_code'] ?? null,
            'payment_type' => $response['type'] ?? 'QR',
            'amount_mvr' => $amount,
            'currency' => $response['currency'] ?? 'MVR',
            'status' => $savedStatus,
            'escrow_state' => Payment::ESCROW_HOLDING,
            'charged_at' => $savedStatus === Payment::STATUS_COMPLETED ? now() : null,
            'platform_commission' => $commission,
            'provider_net' => $providerNet,
            'tgst_amount' => $tgst,
            'swipe_payload' => $response,
        ]);
    }

    public function releaseEscrow(Payment $payment): void
    {
        $payment->update([
            'escrow_state' => Payment::ESCROW_RELEASED,
            'released_at' => now(),
        ]);

        Log::info('swipe.escrow.released', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'provider_net' => $payment->provider_net,
        ]);
    }

    public function refund(Payment $payment, ?string $reason = null): void
    {
        $payment->update([
            'status' => Payment::STATUS_REFUNDED,
            'escrow_state' => Payment::ESCROW_REFUNDED,
            'refunded_at' => now(),
        ]);

        Log::info('swipe.refund.requested', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'reason' => $reason,
        ]);
    }

    public function commissionRate(string $category): float
    {
        return match ($category) {
            'eat', 'wash' => 0.08,
            'buy' => 0.10,
            'experience' => 0.15,
            default => 0.12,
        };
    }

    protected function mockChargeResponse(Order $order, float $amount): array
    {
        try {
            $process = new Process([
                'swipe',
                'payments',
                'create',
                '--amount', number_format($amount, 2, '.', ''),
                '--currency', 'MVR',
                '--type', 'QR',
                '--output', 'json',
            ]);
            $process->setTimeout(20);
            $process->run();

            if ($process->isSuccessful()) {
                $payload = json_decode($process->getOutput(), true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($payload) && isset($payload['id'])) {
                    return $payload;
                }

                Log::warning('swipe.mock.cli.invalid_json', [
                    'output' => $process->getOutput(),
                    'error' => $process->getErrorOutput(),
                ]);
            } else {
                Log::warning('swipe.mock.cli.failed', [
                    'exit_code' => $process->getExitCode(),
                    'error' => $process->getErrorOutput(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('swipe.mock.cli.exception', ['message' => $e->getMessage()]);
        }

        return [
            'id' => 'mock_'.bin2hex(random_bytes(8)),
            'reference' => 'MOCK'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 6)),
            'short_code' => strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
            'type' => 'QR',
            'amount' => $amount,
            'currency' => 'MVR',
            'status' => 'COMPLETED',
            'qr_data' => $this->mockQrPayload($order, $amount),
            'created_at' => Carbon::now()->toIso8601String(),
        ];
    }

    protected function mockQrPayload(Order $order, float $amount): string
    {
        return base64_encode(sprintf(
            'EMV_MOCK|order=%s|amount=%.2f|currency=MVR|voucher=%s',
            $order->reference,
            $amount,
            $order->voucher_code,
        ));
    }

    protected function liveCharge(Order $order, float $amount): array
    {
        $token = $this->fetchAccessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl.'/api/v1/payments', [
                'amount' => $amount,
                'currency' => strtoupper($order->currency ?? 'MVR'),
                'type' => 'LINK',
                'description' => sprintf('Order %s', $order->reference),
                'recipient_vpa' => '',
            ])
            ->throw()
            ->json();

        return $response;
    }

    protected function fetchAccessToken(): string
    {
        return cache()->remember('swipe.token', now()->addMinutes(50), function () {
            if (! $this->clientId || ! $this->clientSecret) {
                throw new \RuntimeException('Swipe client credentials are not configured. Please set SWIPE_CLIENT_ID and SWIPE_CLIENT_SECRET in your .env file.');
            }

            return Http::asForm()
                ->post($this->baseUrl.'/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ])
                ->throw()
                ->json('access_token');
        });
    }
}
