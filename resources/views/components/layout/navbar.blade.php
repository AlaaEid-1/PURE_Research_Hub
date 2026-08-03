<nav x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 w-full border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand Logo -->
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-blue-600/30 group-hover:scale-105 transition-transform">
                        P
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">PURE</span>
                        <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 tracking-wider uppercase -mt-1">Research Hub</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}">
                        Home
                    </a>
                    <a href="{{ route('research.index') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('research.*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}">
                        Research Catalog
                    </a>
                    <a href="{{ route('categories.index') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}">
                        Categories
                    </a>
                    <a href="{{ route('about') }}" class="text-sm font-medium transition-colors {{ request()->routeIs('about') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}">
                        About Platform
                    </a>
                </div>
            </div>

            <!-- Right Nav Actions -->
            <div class="hidden md:flex items-center gap-4">
                <!-- Dark Mode Toggle Button -->
                <button 
                    @click="$store.darkMode.toggle()" 
                    type="button"
                    class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none"
                    aria-label="Toggle dark mode"
                >
                    <template x-if="!$store.darkMode.on">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </template>
                    <template x-if="$store.darkMode.on">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </template>
                </button>

                @auth
                    <!-- Notification Bell Icon -->
                    <a href="{{ route('dashboard.notifications.index') }}" class="relative p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-blue-600 ring-2 ring-white dark:ring-slate-900"></span>
                        @endif
                    </a>

                    <!-- User Account Dropdown (Inline Alpine) -->
                    <div class="relative" x-data="{ open: false }">
                        <!-- Trigger Button -->
                        <button type="button" @click="open = !open" class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors focus:outline-none cursor-pointer">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-lg object-cover ring-2 ring-blue-600/30" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                            <div class="text-left hidden lg:block">
                                <p class="text-xs font-semibold text-slate-900 dark:text-white leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ auth()->user()->institution ?? 'Academic Researcher' }}</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Menu Panel -->
                        <div
                            x-show="open"
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-transition
                            x-cloak
                            class="absolute right-0 mt-2 w-56 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-xl z-50"
                        >
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                Researcher Dashboard
                            </a>

                            <a href="{{ route('dashboard.research.index') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                My Research Papers
                            </a>

                            <a href="{{ route('dashboard.bookmarks.index') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                Saved Collection
                            </a>

                            <a href="{{ route('dashboard.analytics') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                Impact Analytics
                            </a>

                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                Profile Settings
                            </a>

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    Admin Portal
                                </a>
                            @endif

                            <div class="border-t border-slate-100 dark:border-slate-800"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2">
                        Sign In
                    </a>
                    <x-ui.button variant="primary" size="sm" onclick="window.location.href='{{ route('register') }}'">
                        Join Network
                    </x-ui.button>
                @endauth
            </div>

            <!-- Mobile Menu Hamburger Button -->
            <div class="flex items-center gap-2 md:hidden">
                <button 
                    @click="$store.darkMode.toggle()" 
                    type="button"
                    class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" class="md:hidden border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-2 pb-4 space-y-2">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
            Home
        </a>
        <a href="{{ route('research.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
            Research Catalog
        </a>
        <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
            Categories
        </a>

        @auth
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-blue-600 dark:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Dashboard
                </a>
                <a href="{{ route('dashboard.bookmarks.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Saved Collection
                </a>
                <a href="{{ route('dashboard.analytics') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Impact Analytics
                </a>
                <a href="{{ route('dashboard.notifications.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Notifications
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-base font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">
                        Log Out
                    </button>
                </form>
            </div>
        @else
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full text-center px-4 py-2 rounded-xl text-sm font-medium border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200">
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="w-full text-center px-4 py-2 rounded-xl text-sm font-medium bg-blue-600 text-white">
                    Join Network
                </a>
            </div>
        @endauth
    </div>
</nav>
