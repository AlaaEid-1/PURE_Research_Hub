<x-guest-layout title="PURE Research Hub - Modern Academic Publication Platform">
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 border-b border-slate-200/80 dark:border-slate-800/80">
        <!-- Background Decorative Glow -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-blue-500/10 dark:bg-blue-600/10 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800/60 text-blue-700 dark:text-blue-300 text-xs font-semibold mb-8">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                The Next Generation Research & Academic Discovery Network
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white max-w-4xl mx-auto leading-tight">
                Publish, Discover & Showcase Academic Research with <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-cyan-500 to-blue-700">PURE Impact</span>.
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Connect with global researchers, track citation statistics, and showcase peer-reviewed publications on a modern academic platform.
            </p>

            <!-- Main Search Bar -->
            <div class="mt-10 max-w-3xl mx-auto">
                <x-search-bar placeholder="Search by paper title, DOI, author name, or research discipline..." />
                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                    Try searching for: <span class="font-medium text-slate-700 dark:text-slate-300">Artificial Intelligence</span>, <span class="font-medium text-slate-700 dark:text-slate-300">Quantum Computing</span>, or <span class="font-medium text-slate-700 dark:text-slate-300">Genomics</span>
                </p>
            </div>

            <!-- Hero CTAs -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <x-ui.button variant="primary" size="lg" onclick="window.location.href='{{ route('register') }}'">
                    Publish Your Research
                </x-ui.button>
                <x-ui.button variant="outline" size="lg" onclick="window.location.href='{{ route('about') }}'">
                    Learn More About PURE
                </x-ui.button>
            </div>
        </div>
    </section>

    <!-- Platform Statistics Section -->
    <section class="py-12 bg-white/50 dark:bg-slate-900/50 border-b border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <x-cards.stat-card title="Total Papers" value="{{ $stats['total_publications'] }}" subtitle="Open access publications" icon="document" trend="+14% this month" />
                <x-cards.stat-card title="Researchers" value="{{ $stats['total_researchers'] }}" subtitle="Verified academics" icon="users" trend="+22% this month" />
                <x-cards.stat-card title="Institutions" value="{{ $stats['institutions'] }}" subtitle="Global universities" icon="building" />
                <x-cards.stat-card title="Citations" value="{{ $stats['citations'] }}" subtitle="Tracked academic citations" icon="quote" trend="+8% growth" />
            </div>
        </div>
    </section>

    <!-- Platform Introduction Section -->
    <section class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-xs font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-2">Built For Researchers</h2>
                <h3 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Engineered for Open Science and Academic Excellence
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                    <div class="w-12 h-12 rounded-xl bg-blue-600/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Research Publication</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Share your preprints, peer-reviewed articles, and research datasets directly with the global academic community.
                    </p>
                </div>

                <!-- Feature Card 2 -->
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Verified Researcher Profiles</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Showcase your institutional affiliations, publications, ORCID identity, and citation metrics in one sleek profile.
                    </p>
                </div>

                <!-- Feature Card 3 -->
                <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                    <div class="w-12 h-12 rounded-xl bg-slate-900/10 dark:bg-slate-100/10 text-slate-900 dark:text-slate-100 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Instant Academic Discovery</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Discover breakthrough research across disciplines with clean filters, citations, and instant full-text previews.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Research Publications Placeholder Section -->
    <section class="py-16 bg-slate-100/50 dark:bg-slate-900/30 border-y border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Latest Research Publications</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Discover recent academic contributions</p>
                </div>
            </div>

            <!-- Reusable Empty State Component for Clean Foundation -->
            <x-ui.empty-state 
                title="Research Index Ready" 
                description="The publication index is ready for paper submissions. Researchers can publish and index papers in the upcoming release phase." 
                icon="document"
            >
                <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('register') }}'">
                    Create Account to Publish
                </x-ui.button>
            </x-ui.empty-state>
        </div>
    </section>

    <!-- Featured Researchers Placeholder Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Featured Academic Researchers</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Connect with scholars and leading minds</p>
                </div>
            </div>

            <x-ui.empty-state 
                title="Academic Directory Initialized" 
                description="Join PURE Research Hub today to build your verified academic profile and connect with researchers worldwide." 
                icon="users"
            >
                <x-ui.button variant="outline" size="md" onclick="window.location.href='{{ route('register') }}'">
                    Register Academic Profile
                </x-ui.button>
            </x-ui.empty-state>
        </div>
    </section>

    <!-- Call To Action Section -->
    <section class="py-20 bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 text-white relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-4">
                Ready to Share Your Research With The World?
            </h2>
            <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto mb-8">
                Join thousands of scholars and institutions using PURE Research Hub for open, accessible academic publication.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <x-ui.button variant="primary" size="lg" onclick="window.location.href='{{ route('register') }}'" class="bg-blue-600 hover:bg-blue-500">
                    Get Started Free
                </x-ui.button>
                <x-ui.button variant="outline" size="lg" onclick="window.location.href='{{ route('contact') }}'" class="border-slate-600 text-white hover:bg-slate-800">
                    Contact Academic Desk
                </x-ui.button>
            </div>
        </div>
    </section>
</x-guest-layout>
