<x-admin-layout title="User Profile — Admin">
    <div class="space-y-6 max-w-4xl">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 hover:underline">&larr; Back to Users</a>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">{{ $user->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Profile Info --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-blue-500/20" onerror="this.src='{{ asset('images/avatar-fallback.svg') }}'">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ $user->name }}</h2>
                            <p class="text-xs text-slate-500">{{ $user->institution ?? 'No institution set' }}</p>
                            @if($user->department)
                                <p class="text-xs text-slate-400">{{ $user->department }}</p>
                            @endif
                        </div>
                    </div>

                    @if($user->bio)
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $user->bio }}</p>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-center">
                            <p class="font-bold text-lg text-slate-900 dark:text-white">{{ $user->researches_count }}</p>
                            <p class="text-slate-400">Papers</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-center">
                            <p class="font-bold text-lg text-slate-900 dark:text-white">{{ $user->sent_access_requests_count }}</p>
                            <p class="text-slate-400">Access Requests Sent</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-center">
                            <p class="font-bold text-lg text-slate-900 dark:text-white">{{ $user->created_at->format('M Y') }}</p>
                            <p class="text-slate-400">Member Since</p>
                        </div>
                    </div>
                </div>

                {{-- Recent Papers --}}
                @if($user->researches->count() > 0)
                    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-6 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Recent Papers</h3>
                        <div class="space-y-2">
                            @foreach($user->researches as $paper)
                                <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-xs">
                                    <a href="{{ route('admin.research.show', $paper) }}" class="font-semibold text-slate-900 dark:text-white hover:text-blue-600 truncate">{{ $paper->title }}</a>
                                    <span class="px-2 py-0.5 rounded-full border shrink-0 {{ $paper->status->badgeClasses() }}">{{ $paper->status->label() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Admin Controls --}}
            <div class="space-y-4">
                {{-- Role Management --}}
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-5 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Role</h3>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->isAdmin() ? 'bg-purple-100 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300' : 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300' }}">
                            {{ $user->role->label() }}
                        </span>
                    </div>

                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="space-y-2">
                            @csrf
                            <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                                @foreach(\App\Enums\Role::cases() as $r)
                                    <option value="{{ $r->value }}" {{ $user->role === $r ? 'selected' : '' }}>{{ $r->label() }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">Update Role</button>
                        </form>
                    @else
                        <p class="text-xs text-slate-400 italic">You cannot change your own role.</p>
                    @endif
                </div>

                {{-- Profile Links --}}
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-5 space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Links</h3>
                    <a href="{{ route('researchers.show', $user) }}" target="_blank" class="flex items-center gap-2 text-xs text-blue-600 hover:underline">
                        View Public Profile &rarr;
                    </a>
                    @if($user->orcid_id)
                        <a href="https://orcid.org/{{ $user->orcid_id }}" target="_blank" class="flex items-center gap-2 text-xs text-blue-600 hover:underline">ORCID: {{ $user->orcid_id }}</a>
                    @endif
                    @if($user->google_scholar_url)
                        <a href="{{ $user->google_scholar_url }}" target="_blank" class="flex items-center gap-2 text-xs text-blue-600 hover:underline">Google Scholar</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
