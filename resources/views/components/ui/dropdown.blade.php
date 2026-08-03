@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-xl',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-bottom-top',
        'right' => 'origin-top-right right-0',
        default => 'origin-top-right right-0',
    };

    $widthClass = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => 'w-48',
    };
@endphp

<div wire:ignore>
    <div x-data="{ open: false }" 
         @click.outside="open = false" 
         @keydown.escape.window="open = false"
         class="relative">
         
        <button type="button" @click.stop="open = !open" class="w-full text-left">
            {{ $trigger }}
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $widthClass }} rounded-xl shadow-xl {{ $alignmentClasses }}"
            x-cloak
            @click="open = false"
        >
            <div class="rounded-xl {{ $contentClasses }}">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
