<x-app-layout title="Notification Center - PURE Research Hub">
    <div class="space-y-8 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Notification Center</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Updates on PDF access requests, publication reviews, and platform activity.</p>
            </div>

            @if(auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('dashboard.notifications.mark-all-read') }}">
                    @csrf
                    <x-ui.button type="submit" variant="outline" size="sm">
                        Mark All as Read
                    </x-ui.button>
                </form>
            @endif
        </div>

        @if($notifications->count() > 0)
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card flex items-start justify-between gap-4 {{ $notification->read_at ? 'opacity-75' : 'ring-2 ring-blue-500/20' }}">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $notification->read_at ? 'bg-slate-300' : 'bg-blue-600' }}"></span>
                                <h3 class="text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $notification->data['research_title'] ?? 'Platform Notification' }}
                                </h3>
                                <span class="text-[10px] text-slate-400">&bull; {{ $notification->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed pl-4">
                                @if(isset($notification->data['requester_name']))
                                    <strong class="text-slate-900 dark:text-white">{{ $notification->data['requester_name'] }}</strong> requested access: "{{ $notification->data['message'] }}"
                                @elseif(isset($notification->data['status']))
                                    Access request status updated to: <span class="font-semibold text-blue-600 uppercase">{{ $notification->data['status'] }}</span>
                                @else
                                    {{ json_encode($notification->data) }}
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('dashboard.notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400 font-semibold text-[11px] hover:bg-blue-100">
                                        Mark Read
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('dashboard.notifications.destroy', $notification->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <x-ui.empty-state 
                title="No Notifications" 
                description="You have no notifications in your inbox." 
                icon="document"
            />
        @endif
    </div>
</x-app-layout>
