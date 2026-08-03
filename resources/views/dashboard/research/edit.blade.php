<x-app-layout title="Edit Research Paper - PURE Research Hub">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Edit Research Paper</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Update paper details, manuscript file, access permissions, or resubmit for moderation.</p>
        </div>

        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card">
            <form action="{{ route('dashboard.research.update', $research) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Paper Title *</label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $research->title) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Category & Permission -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Research Category</label>
                        <select name="category_id" id="category_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Discipline Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $research->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="download_permission" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Download Access Permission *</label>
                        <select name="download_permission" id="download_permission" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="free" {{ old('download_permission', $research->download_permission->value) == 'free' ? 'selected' : '' }}>Free Open Access Download</option>
                            <option value="request_access" {{ old('download_permission', $research->download_permission->value) == 'request_access' ? 'selected' : '' }}>Require PDF Access Request</option>
                            <option value="contact_author" {{ old('download_permission', $research->download_permission->value) == 'contact_author' ? 'selected' : '' }}>Contact Author for Inquiry</option>
                            <option value="restricted" {{ old('download_permission', $research->download_permission->value) == 'restricted' ? 'selected' : '' }}>Restricted Access (No Downloads)</option>
                        </select>
                        @error('download_permission') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Abstract -->
                <div>
                    <label for="abstract" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Abstract *</label>
                    <textarea name="abstract" id="abstract" rows="5" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">{{ old('abstract', $research->abstract) }}</textarea>
                    @error('abstract') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Keywords & DOI -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="keywords" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Keywords (comma-separated)</label>
                        <input type="text" name="keywords" id="keywords" value="{{ old('keywords', $research->keywords) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('keywords') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="doi" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">DOI (Digital Object Identifier)</label>
                        <input type="text" name="doi" id="doi" value="{{ old('doi', $research->doi) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('doi') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Publication Date & Copyright -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="publication_date" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Publication Date</label>
                        <input type="date" name="publication_date" id="publication_date" value="{{ old('publication_date', $research->publication_date ? $research->publication_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('publication_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="copyright_information" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Copyright Statement</label>
                        <input type="text" name="copyright_information" id="copyright_information" value="{{ old('copyright_information', $research->copyright_information) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('copyright_information') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- File Uploads Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <!-- Replace PDF File Optional (Up to 100MB) -->
                    <div>
                        <label for="pdf_file" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Replace PDF File (Optional, Max 100MB)</label>
                        <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white">
                        <p class="text-[10px] text-slate-400 mt-1">Leave empty to keep current PDF. Up to <strong>100MB</strong></p>
                        @error('pdf_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Replace Thumbnail Cover Image (Up to 5MB) -->
                    <div>
                        <label for="thumbnail_file" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Replace Thumbnail Cover Image (Optional, Max 5MB)</label>
                        <input type="file" name="thumbnail_file" id="thumbnail_file" accept="image/jpeg,image/png,image/webp" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-white">
                        <p class="text-[10px] text-slate-400 mt-1">Leave empty to keep current thumbnail. Up to <strong>5MB</strong></p>
                        @error('thumbnail_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit & Draft Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <x-ui.button type="button" variant="outline" size="md" onclick="window.location.href='{{ route('dashboard.research.index') }}'">
                        Cancel
                    </x-ui.button>
                    <button type="submit" name="submit_action" value="draft" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-colors">
                        Save Draft
                    </button>
                    <button type="submit" name="submit_action" value="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-colors">
                        Save & Resubmit Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
