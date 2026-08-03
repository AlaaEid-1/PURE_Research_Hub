<x-guest-layout title="Academic Categories - PURE Research Hub">
    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Academic Research Categories</h1>
            <p class="text-slate-600 dark:text-slate-400 text-sm">Explore peer-reviewed publications across specialized scientific disciplines and academic fields.</p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm hover:shadow-md transition-all duration-200 glass-card flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-base group-hover:scale-105 transition-transform">
                                {{ substr($category->name, 0, 1) }}
                            </span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                {{ number_format($category->researches_count) }} {{ Str::plural('Paper', $category->researches_count) }}
                            </span>
                        </div>

                        <h2 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            <a href="{{ route('categories.show', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                        </h2>

                        <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                            {{ $category->description ?? 'Explore scientific papers and scholarly research in ' . $category->name . '.' }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                        <a href="{{ route('categories.show', $category->slug) }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            Browse Field &rarr;
                        </a>
                        <a href="{{ route('research.index', ['category' => $category->slug]) }}" class="text-[11px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                            Search Catalog
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-guest-layout>
