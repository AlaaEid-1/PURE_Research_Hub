<x-guest-layout :title="$research->title . ' | PURE Research Hub'" :metaDescription="Str::limit($research->abstract, 160)" :ogImage="$research->thumbnailUrl">
    <div x-data="{ 
            accessModalOpen: false, 
            contactModalOpen: false, 
            abstractExpanded: false
         }" 
         class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- 1. Hero Research Header Banner Section -->
        <div class="relative rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl overflow-hidden glass-card p-6 sm:p-10 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    @if($research->category)
                        <a href="{{ route('categories.show', $research->category->slug) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            {{ $research->category->name }}
                        </a>
                    @endif
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $research->download_permission->badgeClasses() }}">
                        {{ $research->download_permission->label() }}
                    </span>
                    @if($research->doi)
                        <a href="https://doi.org/{{ $research->doi }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-mono bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 transition-colors">
                            <span>DOI: {{ $research->doi }}</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    @endif
                </div>

                <div>
                    @auth
                        @php
                            $isBookmarked = auth()->user()->savedResearches()->where('research_id', $research->id)->exists();
                        @endphp
                        <form method="POST" action="{{ route('research.bookmark.toggle', $research) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold transition-all shadow-sm {{ $isBookmarked ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border-amber-300' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                <svg class="w-4 h-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                                {{ $isBookmarked ? 'Saved' : 'Bookmark' }}
                            </button>
                        </form>
                    @endauth
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white leading-tight font-serif tracking-tight">
                {{ $research->title }}
            </h1>

            <!-- Author & Metrics Bar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <!-- Lead Author Info -->
                <div class="flex items-center gap-4">
                    <img src="{{ $research->user->avatar_url ?? '' }}" alt="{{ $research->user->name ?? '' }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-blue-500/20 shadow-md" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                    <div>
                        <a href="{{ isset($research->user->id) ? route('researchers.show', $research->user) : '#' }}" class="text-sm font-bold text-slate-900 dark:text-white hover:underline flex items-center gap-1.5">
                            {{ $research->user->name ?? 'Lead Author' }}
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </a>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $research->user->institution ?? 'PURE Research Scholar' }}</p>
                    </div>
                </div>

                <!-- Counters -->
                <div class="flex items-center md:justify-end gap-6 text-xs text-slate-600 dark:text-slate-300">
                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 px-4 py-2 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">{{ number_format($research->views) }}</p>
                            <p class="text-[10px] text-slate-400 uppercase">Views</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 px-4 py-2 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">{{ number_format($research->downloads) }}</p>
                            <p class="text-[10px] text-slate-400 uppercase">Downloads</p>
                        </div>
                    </div>

                    @if($research->citations && $research->citations->count() > 0)
                        <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/60 px-4 py-2 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{{ number_format($research->citations->count()) }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">Citations</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Two-Column Main Content & Sticky Action Sidebar -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Main Column (2/3 width) -->
            <div class="lg:col-span-2 space-y-8">
                @php
                    $permissionService = app(\App\Services\ResearchPermissionService::class);
                    $canDownload = $permissionService->canDownload(auth()->user(), $research);
                    $userAccessRequest = auth()->check() ? \App\Models\ResearchAccessRequest::where('research_id', $research->id)->where('requester_id', auth()->id())->first() : null;
                @endphp

                <!-- 1. Secure Manuscript Preview Card -->
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl overflow-hidden glass-card p-6 sm:p-8 space-y-4">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-3.5">
                            <div class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 shrink-0 shadow-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">PDF Document</span>
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Published {{ ($research->publication_date ?? $research->created_at)->format('Y') }}</p>
                            </div>
                        </div>

                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold border shrink-0 {{ $research->download_permission->badgeClasses() }}">
                            {{ $research->download_permission->label() }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Verified academic publication manuscript hosted securely on PURE Research Hub. Access permissions and download options are available in the authorization panel.
                    </p>
                </div>

                <!-- 2. Academic Reading Sections (Abstract, Methodology, Results) -->
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl p-6 sm:p-8 space-y-8 glass-card">
                    <!-- 1. Abstract -->
                    <div class="space-y-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            1. Abstract & Introduction
                        </h2>
                        
                        <div class="relative">
                            <p class="text-slate-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed whitespace-pre-line font-sans transition-all duration-300"
                               :class="!abstractExpanded && 'line-clamp-6'">
                                {{ $research->abstract }}
                            </p>

                            @if(strlen($research->abstract) > 400)
                                <button @click="abstractExpanded = !abstractExpanded" type="button" class="mt-3 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                    <span x-text="abstractExpanded ? 'Show Less ▲' : 'Read Full Abstract ▼'"></span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Methodology & Results Overview -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            2. Methodology & Experimental Findings
                        </h3>
                        @if(!empty($research->methodology))
                            <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed whitespace-pre-line font-sans">
                                {{ $research->methodology }}
                            </p>
                        @elseif($canDownload)
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                Complete methodology, mathematical formulations, dataset configurations, and experimental benchmarking results are compiled inside the verified manuscript PDF.
                            </p>
                        @else
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 text-xs text-slate-500 dark:text-slate-400">
                                Full manuscript methodology and experimental results available after access approval.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Keywords & Focus Topics Section -->
                @if($research->keywords)
                    <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl p-6 sm:p-8 space-y-4 glass-card">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Keywords & Focus Topics</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_map('trim', explode(',', $research->keywords)) as $keyword)
                                <a href="{{ route('research.index', ['query' => $keyword]) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-blue-950/50 text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400 text-xs font-semibold transition-colors">
                                    #{{ $keyword }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Author Profile Card Section -->
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl p-6 sm:p-8 glass-card space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Lead Researcher</h3>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
                        <div class="flex items-center gap-4">
                            <img src="{{ $research->user->avatar_url ?? '' }}" alt="{{ $research->user->name ?? '' }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-blue-500/20 shadow-md" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ $research->user->name ?? 'Academic Researcher' }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $research->user->institution ?? 'PURE Scholar' }}</p>
                                @if($research->user && $research->user->researches)
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $research->user->researches()->count() }} Published Papers</p>
                                @endif
                            </div>
                        </div>

                        @if(isset($research->user->id))
                            <a href="{{ route('researchers.show', $research->user) }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 font-bold text-xs transition-colors shrink-0">
                                View Profile &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Sticky Sidebar Column (1/3 width) -->
            <div class="space-y-6">
                <!-- Action & Access Authorization Card (Sticky) -->
                <div class="sticky top-20 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl p-6 sm:p-8 glass-card space-y-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Document Access</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">DOWNLOAD & PERMISSIONS</p>
                    </div>

                    <div class="space-y-3">
                        @if($canDownload)
                            <a href="{{ route('research.download', $research) }}" class="w-full py-3.5 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                {{ $userAccessRequest && $userAccessRequest->status->value === 'approved' ? 'Download Approved PDF' : 'Download PDF Manuscript' }}
                            </a>
                        @elseif($research->download_permission->value === 'request_access')
                            @auth
                                @if($userAccessRequest && $userAccessRequest->status->value === 'pending')
                                    <div class="w-full py-3 px-4 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 text-xs font-bold border border-amber-200/50 text-center">
                                        Access Request Pending Review
                                    </div>
                                @else
                                    <button @click="accessModalOpen = true" class="w-full py-3.5 px-6 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-lg shadow-amber-600/30 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                        Request PDF Access
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="w-full py-3.5 px-6 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-lg shadow-amber-600/30 transition-all flex items-center justify-center gap-2">
                                    Sign In to Request Access
                                </a>
                            @endauth
                        @elseif($research->download_permission->value === 'contact_author')
                            <button @click="contactModalOpen = true" class="w-full py-3.5 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4-4-4z"></path></svg>
                                Contact Researcher
                            </button>
                        @else
                            <div class="w-full py-3 px-4 rounded-2xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 text-xs font-bold border border-red-200/50 text-center">
                                Access Restricted
                            </div>
                        @endif
                    </div>

                    <!-- Metadata List Card -->
                    <div class="space-y-3 pt-6 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Publication Date</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ ($research->publication_date ?? $research->created_at)->format('M d, Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Category</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $research->category->name ?? 'General' }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Format</span>
                            <span class="font-bold text-slate-900 dark:text-white">PDF Document</span>
                        </div>

                        @if($research->copyright_information)
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 block mb-1">Copyright & License</span>
                                <span class="font-medium text-slate-800 dark:text-slate-300 block leading-tight">{{ $research->copyright_information }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Related Research Section -->
        @if(isset($relatedResearch) && $relatedResearch->isNotEmpty())
            <div class="pt-10 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white font-serif">Related Publications</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Explore similar research papers in this category</p>
                    </div>
                    @if($research->category)
                        <a href="{{ route('categories.show', $research->category->slug) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            View All in {{ $research->category->name }} &rarr;
                        </a>
                    @endif
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedResearch as $related)
                        <x-research-card :research="$related" />
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Modals -->
        <!-- 1. Request Access Modal -->
        @auth
            <div x-show="accessModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div @click.away="accessModalOpen = false" class="w-full max-w-lg p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Request PDF Access</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Explain your academic research intent to the author.</p>

                    <form action="{{ route('research.access-request.store', $research) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="access_message" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Message to Author *</label>
                            <textarea name="message" id="access_message" rows="4" required minlength="10" placeholder="State your academic affiliation, research purpose, or intended use..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <x-ui.button type="button" variant="outline" size="sm" @click="accessModalOpen = false">Cancel</x-ui.button>
                            <x-ui.button type="submit" variant="primary" size="sm">Submit Access Request</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        @endauth

        <!-- 2. Contact Author Modal (Supports Auth & Guest Users) -->
        <div x-show="contactModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="contactModalOpen = false" class="w-full max-w-lg p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Contact Researcher</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Send an academic message or inquiry directly to {{ $research->user->name ?? 'Researcher' }}.</p>

                <form action="{{ route('research.contact-request.store', $research) }}" method="POST" class="space-y-4">
                    @csrf
                    @guest
                        <div>
                            <label for="guest_name" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Your Full Name *</label>
                            <input type="text" name="guest_name" id="guest_name" required placeholder="Dr. Jane Doe" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="guest_email" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Your Email Address *</label>
                            <input type="email" name="guest_email" id="guest_email" required placeholder="jane@university.edu" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                        </div>
                    @endguest

                    <div>
                        <label for="contact_subject" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Subject</label>
                        <input type="text" name="subject" id="contact_subject" value="Inquiry regarding: {{ $research->title }}" placeholder="Subject of inquiry..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="contact_message" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Inquiry Message *</label>
                        <textarea name="message" id="contact_message" rows="4" required minlength="10" placeholder="Type your academic inquiry message here..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <x-ui.button type="button" variant="outline" size="sm" @click="contactModalOpen = false">Cancel</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm">Send Message</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
