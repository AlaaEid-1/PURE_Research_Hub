<x-admin-layout title="Access Requests — Admin">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Access Requests</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">All PDF access requests across the platform.</p>
            </div>
        </div>

        {{-- Status Filter --}}
        <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <a href="{{ route('admin.access-requests.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-colors {{ empty($status) ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">All</a>
            @foreach(\App\Enums\AccessRequestStatus::cases() as $s)
                <a href="{{ route('admin.access-requests.index', ['status' => $s->value]) }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-colors {{ $status === $s->value ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    {{ $s->label() }}
                </a>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Requester</th>
                            <th class="px-5 py-3.5">Research Paper</th>
                            <th class="px-5 py-3.5">Message</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($requests as $ar)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    @if($ar->requester)
                                        <a href="{{ route('admin.users.show', $ar->requester) }}" class="font-semibold text-slate-900 dark:text-white hover:text-blue-600">{{ $ar->requester->name }}</a>
                                        <p class="text-slate-400">{{ $ar->requester->email }}</p>
                                    @else
                                        <span class="text-slate-400">Deleted User</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 max-w-xs">
                                    @if($ar->research)
                                        <a href="{{ route('admin.research.show', $ar->research) }}" class="font-semibold text-slate-900 dark:text-white hover:text-blue-600 block truncate">{{ $ar->research->title }}</a>
                                    @else
                                        <span class="text-slate-400">Deleted Research</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 max-w-xs">
                                    <span class="line-clamp-2">{{ $ar->message ?? '—' }}</span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full border {{ $ar->status->badgeClasses() }}">{{ $ar->status->label() }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $ar->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No access requests found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $requests->withQueryString()->links() }}
    </div>
</x-admin-layout>
