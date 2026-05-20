<div class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-50 font-sans"
     x-data="{
        scrollChat() {
            this.$nextTick(() => {
                const el = this.$refs.stream;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
     }"
     x-on:chat-scroll.window="scrollChat()"
     x-init="scrollChat()"
>
    @if($open)
        <div class="w-[22rem] sm:w-[26rem] max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-card-hover border border-moodhu-200 flex flex-col overflow-hidden" style="height: 540px;">

            <header class="flex items-center justify-between px-4 py-3 border-b border-moodhu-200 bg-gradient-to-r from-madi-600 to-madi-500 text-white">
                <div class="flex items-center gap-2.5">
                    <div class="relative">
                        <x-icons.icon name="sparkles" class="h-5 w-5" />
                        <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-ruh-400 rounded-full ring-2 ring-madi-500"></span>
                    </div>
                    <div>
                        <div class="font-semibold text-sm leading-tight">AfterArrival Concierge</div>
                        <div class="text-[10px] text-madi-100 leading-tight">Powered by Claude</div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button wire:click="clearChat" title="Clear chat" class="text-white/80 hover:text-white hover:bg-white/20 rounded-md p-1.5 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                    <button wire:click="toggle" title="Close" class="text-white/80 hover:text-white hover:bg-white/20 rounded-md p-1.5 transition-colors duration-150">
                        <x-icons.icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            </header>

            <div x-ref="stream" class="flex-1 overflow-y-auto px-3 py-3 space-y-3 bg-moodhu-50 scroll-smooth" id="chat-stream">
                @foreach($messages as $msg)
                    @if($msg['role'] === 'user')
                        <div class="flex justify-end">
                            <div class="max-w-[85%] rounded-2xl rounded-br-sm px-3.5 py-2 text-sm bg-madi-500 text-white shadow-sm">
                                {{ $msg['content'] }}
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start">
                            <div class="max-w-[85%] rounded-2xl rounded-bl-sm px-3.5 py-2.5 text-sm bg-white border border-moodhu-200 text-muraka-900 shadow-sm">
                                <div class="prose prose-sm max-w-none prose-p:my-1 prose-ul:my-1 prose-ol:my-1 prose-li:my-0 prose-headings:my-2 prose-strong:text-madi-700">
                                    {!! Illuminate\Support\Str::markdown($msg['content']) !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($isThinking)
                    <div class="flex justify-start" wire:key="thinking-indicator">
                        <div class="rounded-2xl rounded-bl-sm px-3.5 py-3 bg-white border border-moodhu-200 shadow-sm">
                            <div class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-madi-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></span>
                                <span class="w-1.5 h-1.5 bg-madi-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></span>
                                <span class="w-1.5 h-1.5 bg-madi-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></span>
                                <span class="text-[11px] text-muraka-500 ml-2">Thinking...</span>
                                <button wire:click="abortThinking" title="Cancel" class="ml-2 text-[10px] text-muraka-400 hover:text-red-600 underline">cancel</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if(count($messages) <= 1 && ! $isThinking)
                <div class="px-3 pt-2 pb-1 bg-moodhu-50 border-t border-moodhu-200">
                    <div class="text-[10px] uppercase tracking-label text-muraka-500 mb-1.5">Quick ask</div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($suggestions as $suggestion)
                            <button type="button"
                                    wire:click="quickAsk('{{ addslashes($suggestion) }}')"
                                    @disabled($isThinking)
                                    class="text-[11px] bg-white border border-moodhu-300 hover:border-madi-400 hover:text-madi-700 text-muraka-700 px-2.5 py-1 rounded-full transition-colors duration-150 disabled:opacity-50">
                                {{ $suggestion }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="send" class="border-t border-moodhu-200 p-2.5 flex gap-2 bg-white">
                <input wire:model="input"
                       type="text"
                       placeholder="Ask about food, prices, etiquette…"
                       class="input-field flex-1 text-sm py-2 px-3"
                       autocomplete="off"
                       maxlength="500"
                       @disabled($isThinking)>
                <button type="submit"
                        @disabled($isThinking)
                        class="btn-secondary shrink-0 px-3 py-2 disabled:cursor-not-allowed">
                    Send
                </button>
            </form>

            <p class="text-[10px] text-muraka-400 px-4 pb-2 -mt-1 text-center">AI assistant — confirm important details with the provider.</p>
        </div>
    @else
        <button wire:click="toggle"
                class="group flex items-center gap-2 bg-gradient-to-r from-madi-600 to-madi-500 hover:from-madi-700 hover:to-madi-600 text-white rounded-full shadow-card-hover pl-3.5 pr-5 py-3 transition-all duration-200 hover:shadow-xl">
            <span class="relative">
                <x-icons.icon name="sparkles" class="h-5 w-5" />
                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-ruh-400 rounded-full ring-2 ring-madi-500 group-hover:ring-madi-600 animate-pulse"></span>
            </span>
            <span class="font-semibold text-sm">Ask local</span>
        </button>
    @endif
</div>
