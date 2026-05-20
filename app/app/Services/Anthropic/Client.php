<?php

namespace App\Services\Anthropic;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Client
{
    public const ENDPOINT = 'https://api.anthropic.com/v1/messages';
    public const VERSION = '2023-06-01';

    public function __construct(
        protected ?string $apiKey,
        protected string $model,
        protected bool $mock = true,
    ) {
    }

    public function isAvailable(): bool
    {
        return ! $this->mock && filled($this->apiKey);
    }

    public function model(): string
    {
        return $this->model;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function messages(string $system, array $messages, int $maxTokens = 1024, int $timeoutSeconds = 60): ?string
    {
        if (! $this->isAvailable()) {
            return null;
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => self::VERSION,
            'content-type' => 'application/json',
        ])
            ->timeout($timeoutSeconds)
            ->retry(2, 250, throw: false)
            ->post(self::ENDPOINT, [
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'system' => $system,
                'messages' => $messages,
            ]);

        if ($response->failed()) {
            Log::error('anthropic.client.failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $text = $this->textFromResponse($response->json());

        return $text !== '' ? $text : null;
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public function textFromResponse(?array $data): string
    {
        return collect($data['content'] ?? [])
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');
    }
}
