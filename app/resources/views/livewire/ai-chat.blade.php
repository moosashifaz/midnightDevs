<div class="fixed bottom-6 right-6 z-50 font-sans">
    @if($open)
        <div class="w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col" style="height: 480px;">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-t-2xl">
                <div class="flex items-center gap-2 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="10"/></svg>
                    <span class="font-semibold text-sm">AfterArrival Concierge</span>
                </div>
                <button wire:click="toggle" class="text-white hover:bg-white/20 rounded-md p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3 bg-gray-50 dark:bg-gray-900" id="chat-stream">
                @foreach($messages as $msg)
                    <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-2xl px-3 py-2 text-sm whitespace-pre-line {{ $msg['role'] === 'user' ? 'bg-blue-500 text-white rounded-br-sm' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-600 rounded-bl-sm' }}">
                            {{ $msg['content'] }}
                        </div>
                    </div>
                @endforeach
            </div>
            <form wire:submit.prevent="send" class="border-t border-gray-200 dark:border-gray-700 p-3 flex gap-2 bg-white dark:bg-gray-800 rounded-b-2xl">
                <input wire:model="input" type="text" placeholder="Ask about food, laundry, prices..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-2 focus:ring-cyan-500" autocomplete="off">
                <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg px-3 text-sm font-medium">
                    Send
                </button>
            </form>
            <p class="text-[10px] text-gray-400 px-4 pb-2 -mt-1">AI assistant — confirm important details with the provider.</p>
        </div>
    @else
        <button wire:click="toggle" class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white rounded-full shadow-2xl px-5 py-3 flex items-center gap-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Ask local
        </button>
    @endif
</div>
