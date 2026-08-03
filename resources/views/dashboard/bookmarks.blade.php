<x-app-layout title="Saved Research Bookmarks - PURE Research Hub">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Saved Research Collection</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Your bookmarked research publications for quick academic reference.</p>
            </div>
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
                title="No Saved Researches" 
                description="You haven't bookmarked any research papers yet. Click the bookmark icon on any paper to save it to your collection." 
                icon="document"
            >
                <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('research.index') }}'">
                    Browse Research Catalog
                </x-ui.button>
            </x-ui.empty-state>
        @endif
    </div>
</x-app-layout>
