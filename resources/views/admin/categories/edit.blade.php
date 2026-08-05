<x-admin-layout title="Edit Category — Admin">
    <div class="space-y-6 max-w-xl">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs text-blue-600 hover:underline">&larr; Back to Categories</a>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">Edit Category</h1>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/50 border border-red-200 text-red-700 dark:text-red-300 text-xs space-y-1">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm p-6 space-y-5">
            @csrf @method('PUT')

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-[11px] text-slate-400">Current slug: <span class="font-mono text-slate-500">{{ $category->slug }}</span> — will be updated on save.</p>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
</x-admin-layout>
