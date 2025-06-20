<div>
    @if ($chat)
        <h1 class="text-2xl font-bold mb-4">Chat with {{ $chat->name ?? 'User ' . $chat->id }}</h1>

        {{-- Display chat messages here --}}
        <div class="border rounded-lg p-4 h-96 overflow-y-auto mb-4 bg-gray-50 dark:bg-gray-800">
            @forelse ($chat->messages as $message) {{-- Assuming a 'messages' relationship on your Chat model --}}
                <div class="mb-2 {{ $message->user_id === auth()->id() ? 'text-right' : 'text-left' }}">
                    <span class="inline-block p-2 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                        {{ $message->content }}
                    </span>
                    <br>
                    <small class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p class="text-center text-gray-500">No messages yet. Start the conversation!</p>
            @endforelse
        </div>

        {{-- Chat input form --}}
        {{-- <form wire:submit.prevent="sendMessage" class="flex items-center">
            <input
                type="text"
                wire:model.live="newMessage"
                placeholder="Type your message..."
                class="flex-1 rounded-l-lg border-t border-b border-l text-gray-900 border-gray-300 focus:ring-primary-500 focus:border-primary-500 block min-w-0 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            >
            <button
                type="submit"
                class="px-4 py-2 bg-primary-600 text-white rounded-r-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
                Send
            </button>
        </form> --}}

    @else
        <p>Chat not found.</p>
    @endif
</div>