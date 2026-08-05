<x-guest-layout title="Guest Conversation Thread - PURE Research Hub">
    <div x-data="guestConversationThread({{ $conversation->id }})" class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 border border-amber-200">
                    Secure Guest Access Link
                </span>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-2">
                    {{ $conversation->subject }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Regarding paper: 
                    <a href="{{ route('research.show', $conversation->research->slug) }}" class="font-semibold text-slate-700 dark:text-slate-300 hover:underline">
                        {{ $conversation->research->title }}
                    </a>
                </p>
            </div>

            <div>
                <span class="px-3 py-1.5 rounded-full text-xs font-bold border {{ $conversation->status->value === 'open' ? 'bg-amber-50 text-amber-600 border-amber-200' : ($conversation->status->value === 'replied' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">
                    {{ $conversation->status->label() }}
                </span>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs">
                {{ session('success') }}
            </div>
        @endif

        <!-- Conversation Details Card -->
        <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl p-6 sm:p-8 space-y-6 glass-card">
            <!-- Messages Container -->
            <div id="messages-container" class="space-y-4 max-h-[500px] overflow-y-auto p-2">
                @foreach($conversation->messages as $msg)
                    <div class="p-4 rounded-2xl border space-y-2 transition-all {{ $msg->sender_id === null ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200/70 dark:border-blue-800/70 ml-6 sm:ml-12' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-700/60 mr-6 sm:mr-12' }}">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900 dark:text-white">
                                {{ $msg->sender ? $msg->sender->name : ($conversation->guest_name ?: 'Guest (You)') }}
                            </span>
                            <span class="text-[11px] text-slate-500">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line">{{ $msg->body }}</p>
                    </div>
                @endforeach

                <!-- Dynamic Real-Time Messages -->
                <template x-for="msg in realtimeMessages" :key="msg.id">
                    <div class="p-4 rounded-2xl border space-y-2 transition-all"
                         :class="msg.sender_id === null ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200/70 dark:border-blue-800/70 ml-6 sm:ml-12' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-700/60 mr-6 sm:mr-12'">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900 dark:text-white" x-text="msg.sender_name"></span>
                            <span class="text-[11px] text-slate-500" x-text="msg.formatted_time"></span>
                        </div>
                        <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line" x-text="msg.body"></p>
                    </div>
                </template>
            </div>

            <!-- Reply Form -->
            @if($conversation->status->value !== 'closed')
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Reply to Author</h3>
                    <form @submit="isSending = true" action="{{ request()->fullUrl() }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <textarea name="body" rows="3" required minlength="2" maxlength="3000" placeholder="Type your reply message..." class="w-full px-4 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" :disabled="isSending" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-600/30 transition-all flex items-center gap-2 disabled:opacity-50">
                                <span x-show="!isSending">Send Message</span>
                                <span x-show="isSending" x-cloak>Sending...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('guestConversationThread', (conversationId) => ({
                realtimeMessages: [],
                isSending: false,
                init() {
                    this.scrollToBottom();
                },
                scrollToBottom() {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }
            }));
        });
    </script>
</x-guest-layout>
