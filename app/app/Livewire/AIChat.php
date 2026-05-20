<?php

namespace App\Livewire;

use App\Services\AIAgentService;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AIChat extends Component
{
    public string $input = '';
    public array $messages = [];
    public bool $open = false;

    public function mount(): void
    {
        $this->messages = session('aichat_messages', [
            [
                'role' => 'assistant',
                'content' => "Marhaba! I'm your AfterArrival concierge. Ask me what to eat, where to find laundry, what experiences are good value, or anything about your stay. I'll help.",
            ],
        ]);
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function send(AIAgentService $agent): void
    {
        $trimmed = trim($this->input);
        if ($trimmed === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $trimmed];
        $this->input = '';

        $reply = $agent->reply($trimmed, auth()->user());
        $this->messages[] = ['role' => 'assistant', 'content' => $reply['content']];

        session()->put('aichat_messages', $this->messages);
    }

    public function reset_chat(): void
    {
        $this->messages = [[
            'role' => 'assistant',
            'content' => "Cleared. What's next?",
        ]];
        session()->put('aichat_messages', $this->messages);
    }

    public function render()
    {
        return view('livewire.ai-chat');
    }
}
