<x-app-layout title="Author Inquiries - PURE Research Hub">
    <div x-data="{ activeTab: 'received' }" class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Research Inquiries</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage academic inquiries and direct author communications.</p>
            </div>
            
            <!-- Tab Switchers -->
            <div class="flex items-center gap-2 p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 w-fit">
                <button @click="activeTab = 'received'" :class="activeTab === 'received' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-2">
                    Received Inquiries
                    @if($receivedRequests->total() > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-bold">{{ $receivedRequests->total() }}</span>
                    @endif
                </button>
                <button @click="activeTab = 'sent'" :class="activeTab === 'sent' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-2">
                    Sent Inquiries
                    @if($sentRequests->total() > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold">{{ $sentRequests->total() }}</span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs">
                {{ session('success') }}
            </div>
        @endif

        <!-- Received Inquiries Tab -->
        <div x-show="activeTab === 'received'" class="space-y-4">
            <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl overflow-hidden glass-card">
                @if($receivedRequests->isEmpty())
                    <div class="p-12 text-center space-y-3">
                        <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4-4-4z"></path></svg>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">No Received Inquiries</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When researchers contact you about your papers, their messages will appear here.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-semibold border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-4">Research Paper</th>
                                    <th class="px-6 py-4">Sender</th>
                                    <th class="px-6 py-4">Subject & Preview</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($receivedRequests as $request)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white max-w-xs truncate">
                                            <a href="{{ route('research.show', $request->research->slug) }}" class="hover:underline">
                                                {{ $request->research->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $request->sender->avatar_url }}" alt="{{ $request->sender->name }}" class="w-6 h-6 rounded-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                                                <span class="font-medium text-slate-900 dark:text-white">{{ $request->sender->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 max-w-xs">
                                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $request->subject ?: 'Research Inquiry' }}</p>
                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $request->message }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $request->status->value === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-200' : ($request->status->value === 'replied' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">
                                                {{ $request->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('dashboard.inquiries.show', $request) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 font-semibold hover:bg-blue-100 transition-colors">
                                                Open Conversation &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $receivedRequests->appends(['tab' => 'received'])->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Sent Inquiries Tab -->
        <div x-show="activeTab === 'sent'" class="space-y-4">
            <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl overflow-hidden glass-card">
                @if($sentRequests->isEmpty())
                    <div class="p-12 text-center space-y-3">
                        <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">No Sent Inquiries</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">When you contact authors about papers, your sent messages will appear here.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-semibold border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-6 py-4">Research Paper</th>
                                    <th class="px-6 py-4">Author</th>
                                    <th class="px-6 py-4">Subject & Preview</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($sentRequests as $request)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white max-w-xs truncate">
                                            <a href="{{ route('research.show', $request->research->slug) }}" class="hover:underline">
                                                {{ $request->research->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-medium text-slate-900 dark:text-white">{{ $request->research->user->name ?? 'Author' }}</span>
                                        </td>
                                        <td class="px-6 py-4 max-w-xs">
                                            <p class="font-medium text-slate-900 dark:text-white truncate">{{ $request->subject ?: 'Research Inquiry' }}</p>
                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $request->message }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $request->status->value === 'pending' ? 'bg-amber-50 text-amber-600 border-amber-200' : ($request->status->value === 'replied' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">
                                                {{ $request->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('dashboard.inquiries.show', $request) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 font-semibold hover:bg-blue-100 transition-colors">
                                                View Thread &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $sentRequests->appends(['tab' => 'sent'])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
