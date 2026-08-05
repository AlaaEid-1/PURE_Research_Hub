<x-admin-layout title="Research Details — Admin">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('admin.research.index') }}" class="text-xs text-blue-600 hover:underline">&larr; Back to Research</a>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $research->title }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="px-2.5 py-0.5 rounded-full border text-[11px] {{ $research->status->badgeClasses() }}">{{ $research->status->label() }}</span>
                    <span class="px-2.5 py-0.5 rounded-full border text-[11px] {{ $research->download_permission->badgeClasses() }}">{{ $research->download_permission->label() }}</span>
                    @if($research->category)
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px]">{{ $research->category->name }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.research.edit', $research) }}" class="shrink-0 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                Edit Metadata
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Abstract --}}
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-6 space-y-3">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Abstract</h2>
                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $research->abstract }}</p>
                </div>

                @if($research->keywords)
                    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-6 space-y-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Keywords</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_map('trim', explode(',', $research->keywords)) as $kw)
                                <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs">#{{ $kw }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Access Requests --}}
                @if($research->accessRequests->count() > 0)
                    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-6 space-y-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Access Requests ({{ $research->accessRequests->count() }})</h2>
                        <div class="space-y-2">
                            @foreach($research->accessRequests as $ar)
                                <div class="flex items-center justify-between text-xs p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $ar->requester->name ?? 'Unknown' }}</span>
                                    <span class="px-2 py-0.5 rounded-full border {{ $ar->status->badgeClasses() }}">{{ $ar->status->label() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                {{-- Moderation Actions --}}
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-5 space-y-3">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Moderation Actions</h2>

                    @if($research->status->value !== 'published')
                        <form method="POST" action="{{ route('admin.research.approve', $research) }}">
                            @csrf
                            <button class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">Approve & Publish</button>
                        </form>
                    @endif

                    @if($research->status->value !== 'under_review')
                        <form method="POST" action="{{ route('admin.research.request-changes', $research) }}">
                            @csrf
                            <button class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">Request Changes</button>
                        </form>
                    @endif

                    @if($research->status->value !== 'rejected')
                        <form method="POST" action="{{ route('admin.research.reject', $research) }}">
                            @csrf
                            <button class="w-full py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-colors">Reject</button>
                        </form>
                    @endif

                    @if($research->status->value !== 'archived')
                        <form method="POST" action="{{ route('admin.research.archive', $research) }}">
                            @csrf
                            <button class="w-full py-2 rounded-xl bg-slate-500 hover:bg-slate-600 text-white text-xs font-bold transition-colors">Archive</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.research.destroy', $research) }}" onsubmit="return confirm('Permanently delete this research paper?')">
                        @csrf @method('DELETE')
                        <button class="w-full py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors">Delete Permanently</button>
                    </form>
                </div>

                {{-- Metadata --}}
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-5 space-y-3 text-xs">
                    <h2 class="font-bold uppercase tracking-wider text-slate-500">Details</h2>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex justify-between"><span class="text-slate-400">Author</span><a href="{{ route('admin.users.show', $research->user) }}" class="font-semibold hover:underline">{{ $research->user->name }}</a></div>
                        <div class="flex justify-between"><span class="text-slate-400">Published</span><span class="font-semibold">{{ ($research->publication_date ?? $research->created_at)->format('M d, Y') }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Views</span><span class="font-semibold">{{ number_format($research->views) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Downloads</span><span class="font-semibold">{{ number_format($research->downloads) }}</span></div>
                        @if($research->doi)<div class="flex justify-between"><span class="text-slate-400">DOI</span><span class="font-semibold font-mono">{{ $research->doi }}</span></div>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
