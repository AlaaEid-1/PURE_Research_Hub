@props([
    'placeholder' => 'Search research papers, authors, topics, or DOI...',
    'action' => '#',
])

<form action="{{ $action }}" method="GET" class="w-full">
    <div class="relative flex items-center">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <input 
            type="text" 
            name="query"
            placeholder="{{ $placeholder }}"
            class="w-full pl-11 pr-28 py-3.5 bg-white dark:bg-slate-900/90 text-slate-900 dark:text-white placeholder-slate-400 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-lg shadow-slate-900/5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm sm:text-base"
        >

        <div class="absolute right-2 flex items-center gap-1">
            <x-ui.button type="submit" variant="primary" size="sm" class="rounded-xl px-4 py-2">
                Search
            </x-ui.button>
        </div>
    </div>
</form>
