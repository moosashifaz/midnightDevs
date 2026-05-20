<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Services\AIAgentService;
use Livewire\Attributes\On;
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

    /**
     * Listener — when a tourist taps "Ask the concierge" on a listing or
     * category page, this opens the chat and auto-sends a contextual
     * question about the selected item.
     *
     * Fired from blade with:
     *   Livewire.dispatch('ask-about-listing', { listingId: 42 })
     */
    #[On('ask-about-listing')]
    public function askAboutListing(int $listingId): void
    {
        $listing = Listing::with('provider', 'island')->find($listingId);
        if (! $listing) {
            return;
        }

        $question = sprintf(
            "Tell me about **%s** by %s — is the price (MVR %s ≈ USD %s) fair, and would you recommend it for someone on %s? What should I know before ordering?",
            $listing->title,
            $listing->provider->business_name ?? 'this provider',
            number_format((float) $listing->price_mvr, 0),
            number_format((float) $listing->price_usd, 2),
            $listing->island->name ?? 'this island',
        );

        $this->open = true;
        $this->lastError = null;
        $this->messages[] = ['role' => 'user', 'content' => $question];
        $this->isThinking = true;
        session()->put('aichat_messages', $this->messages);
        $this->dispatch('chat-scroll');
    }

    /**
     * Listener for category-level asks (e.g. "What's good in Eat right now?").
     */
    #[On('ask-about-category')]
    public function askAboutCategory(string $category): void
    {
        $label = Listing::CATEGORIES[$category] ?? ucfirst($category);

        $question = "What would you recommend in the {$label} category right now? Pick your top 2-3 and tell me why.";

        $this->open = true;
        $this->lastError = null;
        $this->messages[] = ['role' => 'user', 'content' => $question];
        $this->isThinking = true;
        session()->put('aichat_messages', $this->messages);
        $this->dispatch('chat-scroll');
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
