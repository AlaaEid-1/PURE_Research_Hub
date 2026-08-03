<x-guest-layout :title="$research->title . ' - PURE Research Hub'" :metaDescription="Str::limit($research->abstract, 160)">
    <!-- Schema.org JSON-LD Structured Data for ScholarlyArticle SEO -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "ScholarlyArticle",
        "headline": {{ json_encode($research->title) }},
        "description": {{ json_encode($research->abstract) }},
        "datePublished": {{ json_encode(($research->publication_date ?? $research->created_at)?->toIso8601String() ?? now()->toIso8601String()) }},
        "author": {
            "@type": "Person",
            "name": {{ json_encode($research->user->name ?? 'Academic Researcher') }},
            "affiliation": {
                "@type": "Organization",
                "name": {{ json_encode($research->user->institution ?? 'PURE Research Hub') }}
            }
        },
        "publisher": {
            "@type": "Organization",
            "name": "PURE Research Hub",
            "url": "{{ url('/') }}"
        },
        "keywords": {{ json_encode($research->keywords) }},
        "sameAs": {{ json_encode($research->doi ? 'https://doi.org/' . $research->doi : null) }}
    }
    </script>

    <div x-data="{ accessModalOpen: false, contactModalOpen: false }" class="py-12 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm flex items-center justify-between">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Header & Hero -->
        <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-0">
                <!-- Thumbnail -->
                <div class="md:col-span-1 bg-slate-100 dark:bg-slate-800 relative min-h-[250px]">
                    <img src="{{ $research->thumbnailUrl ?? asset('images/research-fallback.svg') }}" 
                         alt="{{ $research->title }}" 
                         class="absolute inset-0 w-full h-full object-cover"
                         onerror="this.onerror=null;this.src='{{ asset('images/research-fallback.svg') }}';">
                </div>
                
                <!-- Meta & Info -->
                <div class="md:col-span-2 p-8 sm:p-10 space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            @if($research->category)
                                <a href="{{ route('categories.show', $research->category->slug) }}" class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 hover:bg-blue-100 transition-colors">
                                    {{ $research->category->name }}
                                </a>
                            @endif
                            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $research->download_permission->badgeClasses() }}">
                        {{ $research->download_permission->label() }}
                    </span>
                    @if($research->doi)
                        <span class="text-xs font-mono text-slate-500 dark:text-slate-400">
                            DOI: {{ $research->doi }}
                        </span>
                    @endif
                </div>

                @if(auth()->check())
                    @php
                        $isBookmarked = auth()->user()->savedResearches()->where('research_id', $research->id)->exists();
                    @endphp
                    <form method="POST" action="{{ route('research.bookmark.toggle', $research) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-semibold transition-colors {{ $isBookmarked ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border-amber-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <svg class="w-4 h-4" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                            {{ $isBookmarked ? 'Saved to Collection' : 'Bookmark Paper' }}
                        </button>
                    </form>
                @endif
            </div>

            <!-- Title -->
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight">
                {{ $research->title }}
            </h1>

            <!-- Authors -->
            <div class="flex flex-wrap items-center gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <img src="{{ $research->user->avatar_url ?? '' }}" alt="{{ $research->user->name ?? '' }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-blue-500/20" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                    <div>
                        <a href="{{ isset($research->user->id) ? route('researchers.show', $research->user) : '#' }}" class="text-xs font-bold text-slate-900 dark:text-white hover:underline">
                            {{ $research->user->name ?? 'Lead Author' }}
                        </a>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $research->user->institution ?? 'Lead Author' }}</p>
                    </div>
                </div>

                @if($research->publication_date)
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Published:</span>
                        {{ $research->publication_date->format('F d, Y') }}
                    </div>
                @endif
            </div>

            <!-- Metrics & Citations Bar -->
            <div class="flex flex-wrap items-center gap-6 text-xs text-slate-500 dark:text-slate-400 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="flex items-center gap-1.5 font-medium">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    {{ number_format($research->views) }} Total Views
                </span>
                <span class="flex items-center gap-1.5 font-medium">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    {{ number_format($research->downloads) }} PDF Downloads
                </span>
                <span class="flex items-center gap-1.5 font-semibold text-purple-600 dark:text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Cited by {{ number_format($research->citations->count()) }} papers
                </span>
            </div>
        </div>
        </div> <!-- Close Grid -->
        </div> <!-- Close Hero Container -->

        <!-- Abstract & Details -->
        <div class="p-8 sm:p-10 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card space-y-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Abstract</h2>
            <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed whitespace-pre-line">
                {{ $research->abstract }}
            </p>

            @if($research->keywords)
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">Keywords</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(array_map('trim', explode(',', $research->keywords)) as $keyword)
                            <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium">
                                {{ $keyword }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($research->copyright_information)
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Copyright Statement:</span>
                    {{ $research->copyright_information }}
                </div>
            @endif
        </div>

        <!-- PDF Download & Action Authorization Container -->
        <div class="p-8 sm:p-10 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card space-y-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Full Research Document</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">PDF document access authorization status</p>
                </div>

                @php
                    $permissionService = app(\App\Services\ResearchPermissionService::class);
                    $canDownload = $permissionService->canDownload(auth()->user(), $research);
                    $userAccessRequest = auth()->check() ? \App\Models\ResearchAccessRequest::where('research_id', $research->id)->where('requester_id', auth()->id())->first() : null;
                @endphp

                @if($canDownload)
                    <a href="{{ route('research.download', $research) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm shadow-lg shadow-blue-600/30 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        {{ $userAccessRequest && $userAccessRequest->status->value === 'approved' ? 'Download Approved PDF' : 'Download PDF Document' }}
                    </a>
                @elseif($research->download_permission->value === 'request_access')
                    @if(auth()->check())
                        @if($userAccessRequest && $userAccessRequest->status->value === 'pending')
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 text-xs font-semibold border border-amber-200/50">
                                Access Request Pending Review
                            </span>
                        @else
                            <button @click="accessModalOpen = true" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm shadow-lg shadow-amber-600/30 transition-all">
                                Request PDF Access
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm shadow-lg shadow-amber-600/30 transition-all">
                            Sign In to Request Access
                        </a>
                    @endif
                @elseif($research->download_permission->value === 'contact_author')
                    @if(auth()->check())
                        <button @click="contactModalOpen = true" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm shadow-lg shadow-blue-600/30 transition-all">
                            Contact Researcher
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm shadow-lg shadow-blue-600/30 transition-all">
                            Sign In to Contact Author
                        </a>
                    @endif
                @else
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-200/50">
                        Access Restricted
                    </span>
                @endif
            </div>

            <!-- PDF Preview Placeholder Frame -->
            <div class="w-full h-72 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950 flex flex-col items-center justify-center text-center p-6">
                <svg class="w-16 h-16 text-blue-600/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Protected Document Preview</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm">Use the access actions above to download or request access to the verified manuscript.</p>
            </div>
        </div>

        <!-- Request Access Modal -->
        @if(auth()->check())
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

            <!-- Contact Author Modal -->
            <div x-show="contactModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div @click.away="contactModalOpen = false" class="w-full max-w-lg p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Contact Researcher</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Send an academic message or inquiry directly to {{ $research->user->name ?? 'Researcher' }}.</p>

                    <form action="{{ route('research.contact-request.store', $research) }}" method="POST" class="space-y-4">
                        @csrf
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
        @endif
        
        <!-- Related Research -->
        @if(isset($relatedResearch) && $relatedResearch->isNotEmpty())
            <div class="pt-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Related Research</h2>
                    @if($research->category)
                        <a href="{{ route('categories.show', $research->category->slug) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            View all in {{ $research->category->name }} &rarr;
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
    </div>
</x-guest-layout>
