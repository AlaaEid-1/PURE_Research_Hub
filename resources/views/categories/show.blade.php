<x-guest-layout :title="$category->name . ' Research - PURE Research Hub'">
    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Breadcrumb & Header -->
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <a href="{{ route('categories.index') }}" class="hover:underline">Categories</a>
                <span>&rarr;</span>
                <span class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</span>
            </div>

            <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card space-y-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60">
                    Academic Discipline
                </span>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $category->name }}</h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm max-w-3xl leading-relaxed">
                    {{ $category->description }}
                </p>
            </div>
        </div>

        <!-- Papers Grid -->
        @if($researches->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($researches as $research)
                    <x-cards.research-card :research="$research" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $researches->links() }}
            </div>
        @else
            <x-ui.empty-state 
                title="No Publications in this Field" 
                description="There are currently no research papers published under this category." 
                icon="document"
            >
                @auth
                    <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('dashboard.research.create') }}'">
                        Publish Paper in {{ $category->name }}
                    </x-ui.button>
                @else
                    <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('register') }}'">
                        Register Account to Publish
                    </x-ui.button>
                @endauth
            </x-ui.empty-state>
        @endif
    </div>
</x-guest-layout>
