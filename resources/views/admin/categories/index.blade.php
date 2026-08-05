<x-admin-layout title="Categories — Admin">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Categories</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage research publication categories.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                + New Category
            </a>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Name</th>
                        <th class="px-5 py-3.5">Slug</th>
                        <th class="px-5 py-3.5">Papers</th>
                        <th class="px-5 py-3.5">Description</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-white">
                                <a href="{{ route('categories.show', $category->slug) }}" target="_blank" class="hover:text-blue-600">{{ $category->name }}</a>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-500">{{ $category->slug }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">{{ $category->researches_count }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 max-w-xs truncate">{{ $category->description ?? '—' }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right space-x-1">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-[11px] transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete category \'{{ $category->name }}\'?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-950/50 hover:bg-red-100 text-red-600 dark:text-red-400 font-semibold text-[11px] transition-colors">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No categories yet. <a href="{{ route('admin.categories.create') }}" class="text-blue-600 hover:underline">Create one.</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
