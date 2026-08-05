<x-app-layout title="Inquiry Thread - PURE Research Hub">
    <div x-data="inquiryThread({{ $contactRequest->id }})" class="max-w-4xl mx-auto space-y-8">
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard.inquiries.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 mb-2">
                    &larr; Back to Inquiries
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $contactRequest->subject ?: 'Research Inquiry' }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Regarding paper: 
                    <a href="{{ route('research.show', $contactRequest->research->slug) }}" class="font-semibold text-slate-700 dark:text-slate-300 hover:underline">
                        {{ $contactRequest->research->title }}
                    </a>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-full text-xs font-bold border {{ $contactRequest->status->value === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-200' : ($contactRequest->status->value === 'replied' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">
                    {{ $contactRequest->status->label() }}
                </span>

                @if($contactRequest->status->value !== 'closed')
                    <form action="{{ route('dashboard.inquiries.close', $contactRequest) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Mark this inquiry conversation as closed?')" class="px-3.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-colors">
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
            <!-- Participants Info Header -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100 dark:border-slate-800 text-xs">
                <div class="flex items-center gap-3">
                    <img src="{{ $contactRequest->sender->avatar_url }}" alt="{{ $contactRequest->sender->name }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-blue-500/20" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400">Sender / Inquirer</p>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $contactRequest->sender->name }}</p>
                        <p class="text-slate-500 dark:text-slate-400">{{ $contactRequest->sender->email }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:justify-end">
                    <div class="sm:text-right">
                        <p class="text-[10px] uppercase font-bold text-slate-400">Research Author</p>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $contactRequest->research->user->name ?? 'Author' }}</p>
                        <p class="text-slate-500 dark:text-slate-400">{{ $contactRequest->research->user->institution ?? 'PURE Scholar' }}</p>
                    </div>
                    <img src="{{ $contactRequest->research->user->avatar_url ?? '' }}" alt="{{ $contactRequest->research->user->name ?? '' }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-blue-500/20" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                </div>
            </div>

            <!-- Initial Message Bubble -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Initial Inquiry Message
                    </span>
                    <span>{{ $contactRequest->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">
                    {{ $contactRequest->message }}
                </div>
            </div>

            <!-- Reply Thread Loop -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Message History & Replies</h3>

                <template x-for="reply in replies" :key="reply.id">
                    <div class="p-4 rounded-2xl border space-y-2 transition-all"
                         :class="reply.user_id === {{ auth()->id() }} ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200/70 dark:border-blue-800/70 ml-4 sm:ml-8' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-700/60 mr-4 sm:mr-8'">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900 dark:text-white" x-text="reply.user_name"></span>
                            <span class="text-[11px] text-slate-500" x-text="reply.formatted_time"></span>
                        </div>
                        <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line" x-text="reply.message"></p>
                    </div>
                </template>

                @foreach($contactRequest->replies as $existingReply)
                    <div class="p-4 rounded-2xl border space-y-2 {{ $existingReply->user_id === auth()->id() ? 'bg-blue-50/70 dark:bg-blue-950/40 border-blue-200/70 dark:border-blue-800/70 ml-4 sm:ml-8' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200/60 dark:border-slate-700/60 mr-4 sm:mr-8' }}">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900 dark:text-white">{{ $existingReply->user->name }}</span>
                            <span class="text-[11px] text-slate-500">{{ $existingReply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-slate-800 dark:text-slate-200 whitespace-pre-line">{{ $existingReply->message }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Reply Form -->
            @if($contactRequest->status->value !== 'closed')
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Send Reply</h3>
                    <form action="{{ route('dashboard.inquiries.reply', $contactRequest) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <textarea name="message" rows="3" required minlength="2" placeholder="Write your reply to continue the research conversation..." class="w-full px-4 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <x-ui.button type="submit" variant="primary" size="md">
                                Send Reply
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                    This inquiry thread is closed. No further replies can be posted.
                </div>
            @endif
        </div>
    </div>

    <!-- Realtime Echo Listener Component Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('inquiryThread', (requestId) => ({
                replies: [],
                init() {
                    if (window.Echo) {
                        window.Echo.private(`contact-request.${requestId}`)
                            .listen('ContactReplySent', (e) => {
                                console.log('Realtime reply received:', e);
                                this.replies.push({
                                    id: e.reply_id,
                                    user_id: e.user_id,
                                    user_name: e.user_name,
                                    message: e.message,
                                    formatted_time: e.formatted_time
                                });
                            });
                    }
                }
            }));
        });
    </script>
</x-app-layout>
