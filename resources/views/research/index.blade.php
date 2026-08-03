<x-guest-layout title="Research Publication Catalog - PURE Research Hub">
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Research Discovery Catalog</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Discover, search, and explore peer-reviewed academic papers.</p>
            </div>

            @auth
                <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('dashboard.research.create') }}'">
                    + Publish Paper
                </x-ui.button>
            @endauth
        </div>

        <!-- Search & Filter Form Bar -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card">
            <form method="GET" action="{{ route('research.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-1">
                    <label for="query" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Search Keywords</label>
                    <input type="text" name="query" id="query" value="{{ request('query') }}" placeholder="Title, author, or keyword..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Category</label>
                    <select name="category" id="category" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Permission Filter -->
                <div>
                    <label for="permission" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Access Type</label>
                    <select name="permission" id="permission" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                        <option value="">All Access Types</option>
                        <option value="free" {{ request('permission') == 'free' ? 'selected' : '' }}>Open Access</option>
                        <option value="request_access" {{ request('permission') == 'request_access' ? 'selected' : '' }}>Request Access</option>
                        <option value="contact_author" {{ request('permission') == 'contact_author' ? 'selected' : '' }}>Contact Author</option>
                        <option value="restricted" {{ request('permission') == 'restricted' ? 'selected' : '' }}>Restricted</option>
                    </select>
                </div>

                <!-- Year Filter -->
                <div>
                    <label for="year" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Year</label>
                    <select name="year" id="year" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                        <option value="">All Years</option>
                        @for($i = date('Y'); $i >= 1990; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Sort Filter & Actions -->
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label for="sort" class="block text-[11px] font-semibold uppercase text-slate-500 mb-1">Sort By</label>
                        <select name="sort" id="sort" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest Papers</option>
                            <option value="most_viewed" {{ request('sort') == 'most_viewed' ? 'selected' : '' }}>Most Viewed</option>
                            <option value="most_downloaded" {{ request('sort') == 'most_downloaded' ? 'selected' : '' }}>Most Downloaded</option>
                            <option value="updated" {{ request('sort') == 'updated' ? 'selected' : '' }}>Recently Updated</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md transition-colors">
                        Filter
                    </button>
                    @if(request()->anyFilled(['query', 'category', 'permission', 'year', 'sort']))
                        <a href="{{ route('research.index') }}" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium hover:bg-slate-100 dark:hover:bg-slate-800">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Research Grid -->
        @if($researches->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($researches as $research)
                    <x-research-card :research="$research" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $researches->links() }}
            </div>
        @else
            <x-ui.empty-state 
                title="No Research Publications Found" 
                description="No research papers matched your query or selected filters. Try resetting filters or using different keywords." 
                icon="document"
            >
                <a href="{{ route('research.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold shadow-md hover:bg-blue-700 transition-colors">
                    Clear Search Filters
                </a>
            </x-ui.empty-state>
        @endif
    </div>
</x-guest-layout>
