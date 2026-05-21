<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Exception;

class SwipeCliService
{
    private string $bin;

    public function __construct()
    {
        $this->bin = env('SWIPE_BIN');
    }

    /**
     * Run shell command safely
     */
    private function run(string $command): string
    {
        $process = Process::fromShellCommandline($command);
          $process->setEnv([
                'HOME' => env('HOME'), // or /var/www or storage path
            ]);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new Exception($process->getErrorOutput());
        }

        return trim($process->getOutput());
    }

    /**
     * Prefix swipe CLI
     */
    private function swipe(string $command): string
    {
        return $this->run($this->bin . ' ' . $command);
    }

    /**
     * Create API keys
     */
    public function createKeys(string $name = 'my-test'): array
    {
        $output = $this->swipe(
            "keys create --name \"{$name}\" --scopes wallet:balance,payments:qr --output json"
        );

        $data = json_decode($output, true);

        if (!isset($data['id'], $data['client_secret'])) {
            throw new Exception("Invalid keys response: " . $output);
        }

        return $data;
    }

    /**
     * Login
     */
    public function login(string $clientId, string $clientSecret): void
    {
        $this->swipe(sprintf(
            'auth login --client-id %s --client-secret %s',
            escapeshellarg($clientId),
            escapeshellarg($clientSecret)
        ));
    }

    /**
     * Create payment (NO params needed)
     */
    public function createPayment($amount = 25): array
    {
        $output = $this->swipe(
            "payments create --amount {$amount} --currency USD --type QR --output json"
        );

        $data = json_decode($output, true);

        if (!isset($data['payment_url'])) {
            throw new Exception("Payment URL missing: " . $output);
        }

        return $data;
    }

    /**
     * Full flow → returns payment URL
     */
    public function createQrPayment($amount = 25): string
    {
        $keys = $this->createKeys();

        $this->login(
            $keys['id'],
            $keys['client_secret']
        );

        $payment = $this->createPayment($amount);

        return $payment['payment_url'];
    }
}