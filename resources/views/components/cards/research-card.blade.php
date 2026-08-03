@props([
    'research',
    'showActions' => false,
])

<div {{ $attributes->merge(['class' => 'p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm hover:shadow-md transition-all duration-200 glass-card flex flex-col justify-between']) }}>
    <div class="space-y-4">
        <!-- Category & Access Badge -->
        <div class="flex items-center justify-between gap-2">
            @if($research->category)
                <a href="{{ route('categories.show', $research->category->slug) }}" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60 hover:bg-blue-100 transition-colors">
                    {{ $research->category->name }}
                </a>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                    General Science
                </span>
            @endif

            <span class="text-[11px] font-medium px-2.5 py-0.5 rounded-full border {{ $research->download_permission->badgeClasses() }}">
                {{ $research->download_permission->label() }}
            </span>
        </div>

        <!-- Title -->
        <h3 class="text-lg font-bold text-slate-900 dark:text-white line-clamp-2 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
            <a href="{{ route('research.show', $research->slug) }}">
                {{ $research->title }}
            </a>
        </h3>

        <!-- Authors & Date -->
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            @if($research->user)
                <a href="{{ route('researchers.show', $research->user) }}" class="font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 truncate">
                    {{ $research->user->name }}
                </a>
            @else
                <span class="font-medium text-slate-700 dark:text-slate-300 truncate">Unknown Author</span>
            @endif

            @if($research->publication_date)
                <span>&bull;</span>
                <span>{{ $research->publication_date->format('M Y') }}</span>
            @endif
        </div>

        <!-- Abstract Snippet -->
        <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
            {{ $research->abstract }}
        </p>
    </div>

    <!-- Footer Stats & Actions -->
    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4 text-xs text-slate-400">
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                {{ number_format($research->views) }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                {{ number_format($research->downloads) }}
            </span>
        </div>

        @if($showActions)
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.research.edit', $research) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
                <form method="POST" action="{{ route('dashboard.research.destroy', $research) }}" onsubmit="return confirm('Are you sure you want to delete this publication?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('research.show', $research->slug) }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                Read Paper &rarr;
            </a>
        @endif
    </div>
</div>
