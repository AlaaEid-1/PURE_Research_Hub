<x-app-layout title="My Research Publications - PURE Research Hub">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Research Publications</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage, edit, and publish your academic papers.</p>
            </div>

            <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('dashboard.research.create') }}'">
                + Publish New Paper
            </x-ui.button>
        </div>

        <!-- Research Grid or Empty State -->
        @if($researches->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($researches as $research)
                    <x-research-card :research="$research" :showActions="true" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $researches->links() }}
            </div>
        @else
            <x-ui.empty-state 
                title="No Publications Uploaded" 
                description="You haven't uploaded any research papers yet. Click below to publish your first academic paper." 
                icon="document"
            >
                <x-ui.button variant="primary" size="md" onclick="window.location.href='{{ route('dashboard.research.create') }}'">
                    Publish Paper Now
                </x-ui.button>
            </x-ui.empty-state>
        @endif
    </div>
</x-app-layout>
