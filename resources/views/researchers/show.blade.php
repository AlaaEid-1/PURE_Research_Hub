<x-guest-layout :title="$user->name . ' - Academic Researcher Profile'">
    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Researcher Header Profile Card -->
        <div class="p-8 sm:p-10 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card space-y-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover ring-4 ring-blue-600/30 shadow-md" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">

                <div class="space-y-2 text-center sm:text-left flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h1>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60">
                            Verified Researcher
                        </span>
                    </div>

                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                        {{ $user->department ? $user->department . ' &bull; ' : '' }}{{ $user->institution ?? 'Academic Researcher' }}
                    </p>

                    @if($user->bio)
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed pt-1">
                            {{ $user->bio }}
                        </p>
                    @endif

                    <!-- Academic Identifiers & External Badges -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-2">
                        @if($user->orcid_id)
                            <a href="https://orcid.org/{{ $user->orcid_id }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 text-xs font-semibold border border-emerald-200/60 hover:bg-emerald-100 transition-colors">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                ORCID: {{ $user->orcid_id }}
                            </a>
                        @endif

                        @if($user->google_scholar_url)
                            <a href="{{ $user->google_scholar_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 text-xs font-semibold border border-blue-200/60 hover:bg-blue-100 transition-colors">
                                Google Scholar &rarr;
                            </a>
                        @endif

                        @if($user->website_url)
                            <a href="{{ $user->website_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 transition-colors">
                                Personal Website &rarr;
                            </a>
                        @endif
                    </div>

                    <!-- Research Interests Tags -->
                    @if($user->research_interests)
                        <div class="pt-3 flex flex-wrap items-center gap-1.5">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mr-1">Interests:</span>
                            @foreach(array_map('trim', explode(',', $user->research_interests)) as $interest)
                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px] font-medium">
                                    {{ $interest }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Academic Impact Statistics Bar -->
            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-100 dark:border-slate-800/80 text-center">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total_publications']) }}</p>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">Publications</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <p class="text-xl sm:text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_views']) }}</p>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">Total Views</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50">
                    <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['total_downloads']) }}</p>
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">PDF Downloads</p>
                </div>
            </div>
        </div>

        <!-- Published Papers Section -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Published Research Papers</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Showing {{ $researches->count() }} papers</span>
            </div>

            @if($researches->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($researches as $research)
                        <x-cards.research-card :research="$research" />
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $researches->links() }}
                </div>
            @else
                <x-ui.empty-state 
                    title="No Published Papers" 
                    description="This researcher has not published any research papers yet." 
                    icon="document"
                />
            @endif
        </div>
    </div>
</x-guest-layout>
