@props(['research', 'showActions' => false])

<article class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full">
    <!-- Thumbnail -->
    <a href="{{ route('research.show', $research->slug) }}" class="block relative aspect-[4/3] overflow-hidden bg-gray-100">
        <img src="{{ $research->thumbnailUrl ?? asset('images/research-fallback.svg') }}" 
             alt="{{ $research->title }}" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
             loading="lazy"
             onerror="this.onerror=null;this.src='{{ asset('images/research-fallback.svg') }}';">
        
        <!-- Category Badge Floating on Image -->
        @if($research->category)
            <div class="absolute top-4 left-4">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-blue-700 text-xs font-semibold rounded-full shadow-sm">
                    {{ $research->category->name }}
                </span>
            </div>
        @endif
        
        <!-- Permission Badge -->
        <div class="absolute top-4 right-4">
            @if($research->download_permission->value === 'FREE')
                <span class="px-2 py-1 bg-green-500/90 backdrop-blur-sm text-white text-xs font-bold rounded shadow-sm">FREE</span>
            @elseif($research->download_permission->value === 'RESTRICTED')
                <span class="px-2 py-1 bg-red-500/90 backdrop-blur-sm text-white text-xs font-bold rounded shadow-sm">RESTRICTED</span>
            @else
                <span class="px-2 py-1 bg-amber-500/90 backdrop-blur-sm text-white text-xs font-bold rounded shadow-sm">REQUEST</span>
            @endif
        </div>
    </a>

    <!-- Content -->
    <div class="p-6 flex flex-col flex-grow">
        <div class="flex items-center text-xs text-gray-500 mb-3 space-x-2">
            @php $date = $research->publication_date ?? $research->created_at ?? now(); @endphp
            <time datetime="{{ $date->format('Y-m-d') }}">
                {{ $date->format('M d, Y') }}
            </time>
        </div>
        
        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
            <a href="{{ route('research.show', $research->slug) }}">
                {{ $research->title }}
            </a>
        </h3>
        
        <p class="text-gray-600 text-sm line-clamp-2 mb-4 flex-grow">
            {{ $research->abstract }}
        </p>

        <!-- Footer / Metadata -->
        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs shrink-0">
                    {{ substr($research->user->name ?? 'A', 0, 1) }}
                </div>
                <div class="text-sm truncate max-w-[120px]">
                    <span class="font-medium text-gray-900">{{ $research->user->name ?? 'Unknown Author' }}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-3 text-xs text-gray-500 font-medium">
                <div class="flex items-center space-x-1" title="Views">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>{{ number_format($research->views) }}</span>
                </div>
                <div class="flex items-center space-x-1" title="Downloads">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>{{ number_format($research->downloads) }}</span>
                </div>
            </div>
        </div>
        
        @if($showActions)
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase {{ $research->status->value === 'PUBLISHED' ? 'text-green-600' : 'text-amber-500' }}">
                    {{ $research->status->label() }}
                </span>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard.research.edit', $research) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                        Edit
                    </a>
                    @if(auth()->check() && auth()->id() === $research->user_id)
                        <form action="{{ route('dashboard.research.destroy', $research) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this research? This action cannot be undone and will permanently remove all associated files.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 transition-colors">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
</article>
