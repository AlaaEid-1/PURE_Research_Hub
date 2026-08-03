<x-app-layout title="PDF Access Requests - PURE Research Hub">
    <div class="space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">PDF Access Requests</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review and manage PDF document access requests from fellow researchers.</p>
        </div>

        @if($requests->count() > 0)
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md overflow-hidden glass-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-4">Research Paper</th>
                                <th class="px-6 py-4">Requester</th>
                                <th class="px-6 py-4">Message</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($requests as $req)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <!-- Paper Title -->
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white max-w-xs truncate">
                                        <a href="{{ route('research.show', $req->research->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $req->research->title }}
                                        </a>
                                    </td>

                                    <!-- Requester Info -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $req->requester->avatar_url }}" alt="{{ $req->requester->name }}" class="w-7 h-7 rounded-lg object-cover" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                                            <div>
                                                <a href="{{ route('researchers.show', $req->requester) }}" class="font-bold text-slate-900 dark:text-white hover:underline">
                                                    {{ $req->requester->name }}
                                                </a>
                                                <p class="text-[10px] text-slate-400">{{ $req->requester->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Message -->
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 max-w-md leading-relaxed">
                                        {{ $req->message }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium border {{ $req->status->badgeClasses() }}">
                                            {{ $req->status->label() }}
                                        </span>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                        @if($req->status->value === 'pending')
                                            <form method="POST" action="{{ route('dashboard.requests.approve', $req) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs shadow-sm transition-colors">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('dashboard.requests.reject', $req) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium text-xs shadow-sm transition-colors">
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @else
            <x-ui.empty-state 
                title="No Access Requests" 
                description="You currently have no pending or past PDF access requests for your research publications." 
                icon="document"
            />
        @endif
    </div>
</x-app-layout>
