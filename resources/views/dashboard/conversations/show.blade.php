<x-app-layout title="Conversation Thread - PURE Research Hub">
    <div x-data="conversationThread({{ $conversation->id }})" class="max-w-4xl mx-auto space-y-8">
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard.conversations.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 mb-2">
                    &larr; Back to Conversations
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $conversation->subject }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Regarding paper: 
                    <a href="{{ route('research.show', $conversation->research->slug) }}" class="font-semibold text-slate-700 dark:text-slate-300 hover:underline">
                        {{ $conversation->research->title }}
                    </a>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-full text-xs font-bold border {{ $conversation->status->value === 'open' ? 'bg-amber-50 text-amber-600 border-amber-200' : ($conversation->status->value === 'replied' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">
                    {{ $conversation->status->label() }}
                </span>

                @if($hasAccessGrant)
                    <a href="{{ route('research.download', $conversation->research) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download PDF Paper
                    </a>
                @elseif(auth()->id() === $conversation->author_id || auth()->user()->isAdmin())
                    @if($conversation->sender_id !== null)
                        <form action="{{ route('dashboard.conversations.grant-access', $conversation) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Grant PDF download access to this researcher?')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                Grant Download Access
                            </button>
                        </form>
                    @endif
                @endif

                @if($conversation->status->value !== 'closed')
                    <form action="{{ route('dashboard.conversations.close', $conversation) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Close this conversation thread?')" class="px-3.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-colors">
                            Close Thread
                        </button>
                    </form>
                @endif
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
            <!-- Participants Header -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100 dark:border-slate-800 text-xs">
                <div class="flex items-center gap-3">
                    @if($conversation->sender)
                        <img src="{{ $conversation->sender->avatar_url }}" alt="{{ $conversation->sender->name }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-blue-500/20" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Sender / Inquirer</p>
                            <p class="font-bold text-slate-900 dark:text-white">{{ $conversation->sender->name }}</p>
                            <p class="text-slate-500 dark:text-slate-400">{{ $conversation->sender->email }}</p>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300">G</div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Guest Inquirer</p>
                            <p class="font-bold text-slate-900 dark:text-white">{{ $conversation->guest_name }}</p>
                            <p class="text-slate-500 dark:text-slate-400">{{ $conversation->guest_email }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 sm:justify-end">
                    <div class="sm:text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Research Author</p>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $conversation->author->name }}</p>
                        <p class="text-slate-500 dark:text-slate-400">{{ $conversation->author->institution ?? 'PURE Scholar' }}</p>
                    </div>
                    <img src="{{ $conversation->author->avatar_url }}" alt="{{ $conversation->author->name }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-blue-500/20" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                </div>
            </div>

            <!-- Messages Container with Auto-Scroll -->
            <div id="messages-container" class="space-y-4 max-h-[500px] overflow-y-auto p-2">
                @if($conversation->messages->isEmpty())
                    <div class="p-8 text-center text-xs text-slate-500">
                        No messages in this conversation yet.
                    </div>
                @else
                    @foreach($conversation->messages as $msg)
                        <div class="p-4 rounded-2xl border space-y-2 transition-all {{ $msg->sender_id === auth()->id() ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200/70 dark:border-blue-800/70 ml-6 sm:ml-12' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-700/60 mr-6 sm:mr-12' }}">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-900 dark:text-white">
                                    {{ $msg->sender ? $msg->sender->name : ($conversation->guest_name ?: 'Guest') }}
                                </span>
                                <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                    <span>{{ $msg->created_at->diffForHumans() }}</span>
                                    @if($msg->sender_id === auth()->id())
                                        @if($msg->read_at)
                                            <span class="text-blue-600 font-bold" title="Read {{ $msg->read_at->diffForHumans() }}">✓✓</span>
                                        @else
                                            <span class="text-slate-400" title="Sent">✓</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line">{{ $msg->body }}</p>
                        </div>
                    @endforeach
                @endif

                <!-- Dynamic Real-Time Messages -->
                <template x-for="msg in realtimeMessages" :key="msg.id">
                    <div class="p-4 rounded-2xl border space-y-2 transition-all"
                         :class="msg.sender_id === {{ auth()->id() }} ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200/70 dark:border-blue-800/70 ml-6 sm:ml-12' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-700/60 mr-6 sm:mr-12'">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900 dark:text-white" x-text="msg.sender_name"></span>
                            <span class="text-[11px] text-slate-500" x-text="msg.formatted_time"></span>
                        </div>
                        <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line" x-text="msg.body"></p>
                    </div>
                </template>
                <!-- Typing Indicator Architecture -->
                <div x-show="isTyping" x-cloak class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs text-slate-500 italic flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                    <span>Researcher is typing...</span>
                </div>
            </div>

            <!-- Reply Form -->
            @if($conversation->status->value !== 'closed')
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Reply to Conversation</h3>
                    <form @submit="sendOptimisticMessage($event)" action="{{ route('dashboard.conversations.messages.store', $conversation) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <textarea x-model="newMessageText" name="body" rows="3" required minlength="2" maxlength="3000" placeholder="Type your message here..." class="w-full px-4 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" :disabled="isSending" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-600/30 transition-all flex items-center gap-2 disabled:opacity-50">
                                <span x-show="!isSending">Send Message</span>
                                <span x-show="isSending" x-cloak>Sending...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                    This conversation thread is closed. No further replies can be posted.
                </div>
            @endif
        </div>
    </div>

    <!-- Alpine.js + Laravel Echo Integration Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('conversationThread', (conversationId) => ({
                realtimeMessages: [],
                newMessageText: '',
                isSending: false,
                isTyping: false,
                init() {
                    this.scrollToBottom();

                    if (window.Echo) {
                        window.Echo.private(`conversation.${conversationId}`)
                            .listen('MessageSent', (e) => {
                                console.log('Realtime Message Received:', e);
                                // Prevent duplicate if already optimistically rendered
                                if (!this.realtimeMessages.some(m => m.id === e.id)) {
                                    this.realtimeMessages.push(e);
                                }
                                this.$nextTick(() => this.scrollToBottom());
                            });
                    }
                },
                sendOptimisticMessage(event) {
                    this.isSending = true;
                    this.$nextTick(() => this.scrollToBottom());
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
</x-app-layout>
