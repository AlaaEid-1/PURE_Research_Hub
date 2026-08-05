<x-admin-layout title="User Management — Admin">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Users</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage registered researchers and administrators.</p>
            </div>
        </div>

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, institution…"
                class="flex-1 min-w-48 px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
            <select name="role" class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Roles</option>
                @foreach(\App\Enums\Role::cases() as $r)
                    <option value="{{ $r->value }}" {{ request('role') === $r->value ? 'selected' : '' }}>{{ $r->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">Search</button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 transition-colors">Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">User</th>
                            <th class="px-5 py-3.5">Role</th>
                            <th class="px-5 py-3.5">Institution</th>
                            <th class="px-5 py-3.5">Papers</th>
                            <th class="px-5 py-3.5">Joined</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover shrink-0" onerror="this.src='{{ asset('images/avatar-fallback.svg') }}'">
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                                            <p class="text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $user->isAdmin() ? 'bg-purple-100 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300' : 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300' }}">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">{{ $user->institution ?? '—' }}</td>
                                <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-white">{{ $user->researches_count }}</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('admin.users.show', $user) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition-colors">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $users->withQueryString()->links() }}
    </div>
</x-admin-layout>
