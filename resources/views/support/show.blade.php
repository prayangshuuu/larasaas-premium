<x-app-layout>
    <div class="h-[calc(100vh-65px)] flex flex-col"> {{-- Adjust height based on navbar --}}
        {{-- Header --}}
        <div class="px-6 py-4 bg-zinc-900 border-b border-zinc-800 flex justify-between items-center shrink-0">
            <div>
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    {{ $ticket->subject }}
                    <span class="text-xs font-normal text-zinc-500">#{{ $ticket->ticket_id }}</span>
                </h2>
                <div class="flex items-center gap-2 text-sm mt-1">
                    <span class="text-zinc-400">Status:</span>
                    <span class="capitalize {{ $ticket->status === 'open' || $ticket->status === 'answered' ? 'text-green-400' : 'text-zinc-500' }}">
                        {{ str_replace('_', ' ', $ticket->status) }}
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="text-zinc-400">Priority:</span>
                    <span class="capitalize {{ $ticket->priority === 'high' ? 'text-red-400' : 'text-zinc-400' }}">{{ $ticket->priority }}</span>
                </div>
            </div>
            <a href="{{ route('support.index') }}" class="text-zinc-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </a>
        </div>

        {{-- Chat Area --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-zinc-950/50" id="chat-container">
            @foreach($ticket->messages as $message)
                <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] md:max-w-[70%]">
                        <div class="flex items-end gap-2 {{ $message->user_id === Auth::id() ? 'flex-row-reverse' : 'flex-row' }}">
                            {{-- Avatar --}}
                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold
                                {{ $message->user_id === Auth::id() ? 'bg-indigo-600 text-white' : ($message->user_id ? 'bg-zinc-700 text-zinc-300' : 'bg-green-600 text-white') }}">
                                {{ $message->user_id ? substr($message->user->name ?? 'U', 0, 1) : 'S' }}
                            </div>

                            {{-- Message Bubble --}}
                            <div class="rounded-2xl px-4 py-3 shadow-sm
                                {{ $message->user_id === Auth::id() 
                                    ? 'bg-indigo-600 text-white rounded-br-none' 
                                    : 'bg-zinc-800 text-zinc-300 rounded-bl-none' 
                                }}">
                                <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                                
                                @if($message->attachment_path)
                                    <div class="mt-3 pt-3 border-t {{ $message->user_id === Auth::id() ? 'border-indigo-500' : 'border-zinc-700' }}">
                                        <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="flex items-center gap-2 text-xs hover:underline opacity-80">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            View Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-zinc-500 {{ $message->user_id === Auth::id() ? 'text-right mr-10' : 'ml-10' }}">
                            {{ $message->created_at->format('M d, g:i a') }}
                            @if(!$message->user_id) <span class="text-green-500 ml-1">(System)</span> @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Input Area --}}
        <div class="p-4 bg-zinc-900 border-t border-zinc-800 shrink-0 relative z-20">
            @if($ticket->status === 'closed')
                <div class="text-center py-4 bg-zinc-800/50 rounded-lg border border-zinc-700 text-zinc-400">
                    This ticket is closed. You can no longer reply.
                </div>
            @else
                <form action="{{ route('support.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto">
                    @csrf
                    <div class="relative">
                        <textarea name="message" rows="1" class="block w-full rounded-xl bg-zinc-800 border-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-4 pr-12 py-3 resize-none scrollbar-hide" placeholder="Type your reply..." required oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                        
                        <div class="absolute right-2 bottom-1.5 flex items-center gap-1">
                            <label for="attachment" class="p-2 text-zinc-400 hover:text-white cursor-pointer transition-colors rounded-full hover:bg-zinc-700">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                <input type="file" name="attachment" id="attachment" class="hidden">
                            </label>
                            <button type="submit" class="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-500/20">
                                <svg class="w-5 h-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-4 flex justify-center">
                    <form action="{{ route('support.close', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this ticket?')">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 hover:underline">
                            Mark as Closed
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Scroll to bottom of chat
        const chatContainer = document.getElementById('chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    </script>
</x-app-layout>
