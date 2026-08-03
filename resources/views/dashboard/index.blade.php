<x-app-layout title="Researcher Dashboard - PURE Research Hub">
    <div class="space-y-8">
        <!-- Welcome Banner -->
        <div class="p-8 rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-blue-900 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold uppercase tracking-wider border border-blue-400/20">
                    {{ $stats['verified_status'] }}
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight">Welcome back, {{ $user->name }}!</h1>
                <p class="text-sm text-slate-300">
                    {{ $user->institution ?? 'Academic Researcher' }} @if($user->department) &bull; {{ $user->department }} @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-ui.button variant="accent" size="md" onclick="window.location.href='{{ route('profile.show') }}'">
                    Edit Profile
                </x-ui.button>
                <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('dashboard.research.create') }}'">
                    + New Paper
                </x-ui.button>
            </div>
        </div>

        <!-- Publication Status Cards Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Published Papers -->
            <a href="{{ route('dashboard.research.index') }}" class="group p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:border-emerald-300/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Published</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total_publications']) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Published papers</p>
            </a>

            <!-- Pending Papers -->
            <a href="{{ route('dashboard.research.index') }}" class="group p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:border-amber-300/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase">Pending</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['pending_papers']) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Awaiting review</p>
            </a>

            <!-- Draft Papers -->
            <a href="{{ route('dashboard.research.index') }}" class="group p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:border-slate-300/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Drafts</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['draft_papers']) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Saved drafts</p>
            </a>

            <!-- Rejected Papers -->
            <a href="{{ route('dashboard.research.index') }}" class="group p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm hover:shadow-md hover:border-red-300/50 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-red-100 dark:bg-red-950/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-red-600 dark:text-red-400 uppercase">Rejected</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['rejected_papers']) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Requires revision</p>
            </a>
        </div>

        <!-- Downloads, Views & Access Requests Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Downloads -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-950/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Downloads</span>
                </div>
                <p class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_downloads']) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">PDF downloads across all papers</p>
            </div>

            <!-- Total Views -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Total Views</span>
                </div>
                <p class="text-3xl font-extrabold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_views']) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Article views across all papers</p>
            </div>

            <!-- Pending Access Requests -->
            <a href="{{ route('dashboard.requests.index') }}" class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm hover:border-orange-300/50 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-950/60 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Access Requests</span>
                    @if($stats['pending_access_requests'] > 0)
                        <span class="ml-auto px-2 py-0.5 rounded-full bg-orange-500 text-white text-[10px] font-bold">
                            {{ $stats['pending_access_requests'] }}
                        </span>
                    @endif
                </div>
                <p class="text-3xl font-extrabold text-orange-600 dark:text-orange-400">{{ $stats['pending_access_requests'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pending PDF access requests</p>
            </a>
        </div>

        <!-- Quick Actions & Account Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Links -->
            <div class="lg:col-span-2">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <a href="{{ route('dashboard.research.create') }}" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:bg-blue-50 dark:hover:bg-blue-950/20 transition-colors group">
                            <span class="text-blue-600 dark:text-blue-400 text-lg">📝</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 group-hover:text-blue-600">New Paper</span>
                        </a>
                        <a href="{{ route('dashboard.research.index') }}" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <span class="text-slate-500 text-lg">📚</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">My Papers</span>
                        </a>
                        <a href="{{ route('dashboard.analytics') }}" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:bg-purple-50 dark:hover:bg-purple-950/20 transition-colors group">
                            <span class="text-purple-600 text-lg">📊</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 group-hover:text-purple-600">Analytics</span>
                        </a>
                        <a href="{{ route('dashboard.bookmarks.index') }}" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:bg-yellow-50 dark:hover:bg-yellow-950/20 transition-colors group">
                            <span class="text-yellow-500 text-lg">🔖</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Bookmarks</span>
                        </a>
                        <a href="{{ route('dashboard.notifications.index') }}" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition-colors group">
                            <span class="text-emerald-500 text-lg">🔔</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Notifications</span>
                        </a>
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <span class="text-slate-400 text-lg">⚙️</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Settings</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Summary -->
            <div class="space-y-6">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Account Summary</h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400">Account Type</span>
                            <span class="font-semibold text-slate-900 dark:text-white capitalize">{{ $user->role->value }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400">Member Since</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $user->created_at ? $user->created_at->format('M Y') : 'Recently' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400">Profile Complete</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $stats['profile_completeness'] }}%</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-500 dark:text-slate-400">Account Status</span>
                            <span class="font-semibold text-emerald-600">Active</span>
                        </div>
                    </div>

                    <!-- Profile completeness bar -->
                    <div class="mt-4">
                        <div class="flex justify-between text-[10px] text-slate-400 mb-1">
                            <span>Profile Completeness</span>
                            <span>{{ $stats['profile_completeness'] }}%</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $stats['profile_completeness'] >= 80 ? 'bg-emerald-500' : ($stats['profile_completeness'] >= 50 ? 'bg-blue-500' : 'bg-amber-500') }}" style="width: {{ $stats['profile_completeness'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
