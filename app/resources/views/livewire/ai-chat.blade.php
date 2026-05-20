<div class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-50 font-sans">
    @if($open)
        <div class="w-80 sm:w-96 bg-white rounded-2xl shadow-card-hover border border-moodhu-200 flex flex-col" style="height: 480px;">
            <div class="flex items-center justify-between px-4 py-3 border-b border-moodhu-200 bg-madi-600 rounded-t-2xl">
                <div class="flex items-center gap-2 text-white">
                    <x-icons.icon name="message-circle" class="h-5 w-5" />
                    <span class="font-semibold text-sm">AfterArrival Concierge</span>
                </div>
                <button wire:click="toggle" class="text-white hover:bg-white/20 rounded-md p-1 transition-colors duration-150">
                    <x-icons.icon name="x" class="h-5 w-5" />
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3 bg-moodhu-50" id="chat-stream">
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-2xl px-3 py-2 text-sm whitespace-pre-line {{ $msg['role'] === 'user' ? 'bg-madi-500 text-white rounded-br-sm' : 'bg-white text-muraka-800 border border-moodhu-200 rounded-bl-sm' }}">
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @endforeach
            </div>
            <form wire:submit.prevent="send" class="border-t border-moodhu-200 p-3 flex gap-2 bg-white rounded-b-2xl">
                <input wire:model="input" type="text" placeholder="Ask about food, laundry, prices..." class="input-field flex-1 text-sm" autocomplete="off">
                <button type="submit" class="btn-primary shrink-0 px-3 py-2">Send</button>
            </form>
            <p class="text-[10px] text-muraka-400 px-4 pb-2 -mt-1">AI assistant — confirm important details with the provider.</p>
        </div>
    @else
        <button wire:click="toggle" class="btn-secondary rounded-full shadow-card-hover px-5 py-3 flex items-center gap-2">
            <x-icons.icon name="message-circle" class="h-5 w-5" />
            Ask local
        </button>
    @endif
</div>
