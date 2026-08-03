<x-admin-layout title="Paper Moderation Queue - Admin Control">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Research Publication Moderation Queue</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review, approve, reject, or request revisions on submitted manuscripts.</p>
            </div>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <a href="{{ route('admin.research.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-colors {{ empty($status) ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                All Submissions
            </a>
            <a href="{{ route('admin.research.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-colors {{ $status === 'pending' ? 'bg-amber-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                Pending Review
            </a>
            <a href="{{ route('admin.research.index', ['status' => 'published']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-colors {{ $status === 'published' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                Published
            </a>
            <a href="{{ route('admin.research.index', ['status' => 'under_review']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-colors {{ $status === 'under_review' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                Under Review
            </a>
            <a href="{{ route('admin.research.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-colors {{ $status === 'rejected' ? 'bg-red-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                Rejected
            </a>
        </div>

        <!-- Papers Table -->
        @if($researches->count() > 0)
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md overflow-hidden glass-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200/80 dark:border-slate-800">
                            <tr>
                                <th class="px-6 py-4">Title & Details</th>
                                <th class="px-6 py-4">Author</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Permission</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Moderation Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($researches as $paper)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white max-w-xs truncate">
                                        <a href="{{ route('research.show', $paper->slug) }}" target="_blank" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $paper->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('researchers.show', $paper->user) }}" class="font-bold text-slate-900 dark:text-white hover:underline">
                                            {{ $paper->user->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium">
                                            {{ $paper->category->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full border text-[11px] {{ $paper->download_permission->badgeClasses() }}">
                                            {{ $paper->download_permission->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full border text-[11px] {{ $paper->status->badgeClasses() }}">
                                            {{ $paper->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-1">
                                        @if($paper->status->value !== 'published')
                                            <form method="POST" action="{{ route('admin.research.approve', $paper) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-[11px] shadow-sm">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                        @if($paper->status->value !== 'under_review')
                                            <form method="POST" action="{{ route('admin.research.request-changes', $paper) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-[11px] shadow-sm">
                                                    Review
                                                </button>
                                            </form>
                                        @endif
                                        @if($paper->status->value !== 'rejected')
                                            <form method="POST" action="{{ route('admin.research.reject', $paper) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium text-[11px] shadow-sm">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.research.destroy', $paper) }}" onsubmit="return confirm('Delete this paper permanently?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $researches->links() }}
            </div>
        @else
            <x-ui.empty-state 
                title="No Submissions Found" 
                description="There are currently no research paper submissions matching the selected status." 
                icon="document"
            />
        @endif
    </div>
</x-app-layout>
