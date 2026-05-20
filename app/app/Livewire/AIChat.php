<?php

namespace App\Livewire;

use App\Services\AIAgentService;
use Livewire\Component;

class AIChat extends Component
{
    public string $input = '';

    /**
     * @var array<int, array{role: string, content: string}>
     */
    public array $messages = [];

    public bool $open = false;
    public bool $isThinking = false;
    public ?string $lastError = null;

    /**
     * Quick-ask suggestions shown above the input on the first turn.
     */
    public array $suggestions = [
        'Best dinner under MVR 200?',
        'Is MVR 80 a fair laundry price?',
        'What can I do for 3 hours this afternoon?',
        'Mosque visit dress code?',
    ];

    public function mount(): void
    {
        $this->messages = session('aichat_messages', [
            [
                'role' => 'assistant',
                'content' => "Marhaba! I'm your AfterArrival concierge — ask me about food, laundry, souvenirs, experiences, or anything about your stay. I'll give you straight answers, including whether a price is fair.",
            ],
        ]);
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
        $this->lastError = null;
    }

    /**
     * Submit a user message — appends it to the visible thread, marks the
     * component as "thinking", and triggers fetchReply on the next Livewire
     * tick so the user sees their message + the typing indicator immediately.
     */
    public function send(): void
    {
        $trimmed = trim($this->input);
        if ($trimmed === '' || $this->isThinking) {
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $trimmed];
        $this->input = '';
        $this->isThinking = true;
        $this->lastError = null;
        session()->put('aichat_messages', $this->messages);

        $this->dispatch('chat-scroll');
    }

    /**
     * Performs the actual Anthropic call. Triggered immediately after send()
     * by the wire:poll-once / chat-thinking event so the UI updates first.
     */
    public function fetchReply(AIAgentService $agent): void
    {
        if (! $this->isThinking) {
            return;
        }

        $latest = end($this->messages);
        if (! $latest || $latest['role'] !== 'user') {
            $this->isThinking = false;
            return;
        }

        $history = array_slice($this->messages, 0, -1);

        $reply = $agent->reply($latest['content'], auth()->user(), $history);
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $reply['content'],
        ];
        $this->isThinking = false;

        session()->put('aichat_messages', $this->messages);
        $this->dispatch('chat-scroll');
    }

    public function quickAsk(string $prompt): void
    {
        $this->input = $prompt;
        $this->send();
    }

    public function clearChat(): void
    {
        $this->messages = [[
            'role' => 'assistant',
            'content' => "Cleared. What's next?",
        ]];
        session()->put('aichat_messages', $this->messages);
        $this->isThinking = false;
        $this->lastError = null;
    }

    public function render()
    {
        return view('livewire.ai-chat');
    }
}
