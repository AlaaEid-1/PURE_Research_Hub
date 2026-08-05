<div x-data="notificationsDropdown()" class="relative">
    <!-- Toast Notifications Floating Alert Container -->
    <div class="fixed top-5 right-5 z-50 space-y-3 max-w-sm pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-0">
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 font-bold text-xs">
                    🔔
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 dark:text-white" x-text="toast.title"></p>
                    <p class="text-[11px] text-slate-600 dark:text-slate-400 truncate mt-0.5" x-text="toast.message"></p>
                    <a :href="toast.action_url" class="inline-block text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline mt-1.5">
                        View Details &rarr;
                    </a>
                </div>
                <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    &times;
                </button>
            </div>
        </template>
    </div>

    <!-- Notification Bell Trigger Button -->
    <button @click="toggle()" type="button" class="relative p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        <!-- Unread Badge Counter -->
        <span x-show="unreadCount > 0" x-cloak class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white shadow-sm">
            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
        </span>
    </button>

    <!-- Notification Dropdown Panel -->
    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 sm:w-96 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-2xl z-50 overflow-hidden glass-card">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Notifications</h3>
                <span x-show="unreadCount > 0" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400" x-text="unreadCount + ' new'"></span>
            </div>
            <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                Mark all as read
            </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
            <template x-for="item in items" :key="item.id">
                <div class="p-4 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors flex items-start gap-3" :class="!item.read_at ? 'bg-blue-50/20 dark:bg-blue-950/20' : ''">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 text-sm">
                        📬
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="item.title || 'Notification'"></p>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 truncate mt-0.5" x-text="item.message"></p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[10px] text-slate-400" x-text="item.formatted_time || 'Just now'"></span>
                            <a :href="item.action_url" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">View &rarr;</a>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="items.length === 0" class="p-8 text-center text-xs text-slate-500 dark:text-slate-400 space-y-2">
                <p class="font-semibold">No notifications</p>
                <p class="text-[11px]">When you receive inquiries or replies, they will appear here.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notificationsDropdown', () => ({
            open: false,
            unreadCount: @auth {{ auth()->user()->unreadNotifications->count() }} @else 0 @endauth,
            items: [],
            toasts: [],
            seenIds: new Set(),
            channel: null,
            originalTitle: document.title,
            init() {
                this.updateDocumentTitle();

                if (typeof BroadcastChannel !== 'undefined') {
                    this.channel = new BroadcastChannel('pure_notifications_sync');
                    this.channel.onmessage = (event) => {
                        if (event.data && event.data.type === 'MARK_READ') {
                            this.unreadCount = 0;
                            this.updateDocumentTitle();
                        } else if (event.data && event.data.type === 'NOTIF_RECEIVED') {
                            if (event.data.payload && event.data.payload.id) {
                                this.seenIds.add(event.data.payload.id);
                            }
                        }
                    };
                }

                window.addEventListener('notification-received', (e) => {
                    this.handleNotification(e.detail);
                });
            },
            toggle() {
                this.open = !this.open;
            },
            handleNotification(payload) {
                const notifId = payload.id || (payload.type ? payload.type + '-' + Date.now() : Date.now());
                if (this.seenIds.has(notifId)) {
                    return;
                }
                this.seenIds.add(notifId);

                if (this.channel) {
                    this.channel.postMessage({ type: 'NOTIF_RECEIVED', payload: { id: notifId } });
                }

                this.unreadCount++;
                this.updateDocumentTitle();

                const toast = {
                    id: Date.now() + Math.random(),
                    title: payload.title || 'New Notification',
                    message: payload.message || 'You have received a new update.',
                    action_url: payload.action_url || '#'
                };

                this.toasts.push(toast);
                this.items.unshift(payload);

                setTimeout(() => {
                    this.removeToast(toast.id);
                }, 6000);
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
            markAllAsRead() {
                this.unreadCount = 0;
                this.updateDocumentTitle();
                if (this.channel) {
                    this.channel.postMessage({ type: 'MARK_READ' });
                }
            },
            updateDocumentTitle() {
                if (this.unreadCount > 0) {
                    document.title = `(${this.unreadCount}) ${this.originalTitle}`;
                } else {
                    document.title = this.originalTitle;
                }
            }
        }));
    });
</script>
