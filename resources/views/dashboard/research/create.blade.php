<x-app-layout title="Publish Research Paper - PURE Research Hub">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Publish New Research Paper</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Fill in paper metadata, upload your verified PDF manuscript, and configure access permissions.</p>
        </div>

        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card">
            <form action="{{ route('dashboard.research.store') }}" method="POST" id="uploadForm" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Paper Title *</label>
                    <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="e.g. Deep Learning Applications in Quantum Chemistry" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Category & Permission -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Research Category</label>
                        <select name="category_id" id="category_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Discipline Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="download_permission" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Download Access Permission *</label>
                        <select name="download_permission" id="download_permission" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="free" {{ old('download_permission') == 'free' ? 'selected' : '' }}>Free Open Access Download</option>
                            <option value="request_access" {{ old('download_permission') == 'request_access' ? 'selected' : '' }}>Require PDF Access Request</option>
                            <option value="contact_author" {{ old('download_permission') == 'contact_author' ? 'selected' : '' }}>Contact Author for Inquiry</option>
                            <option value="restricted" {{ old('download_permission') == 'restricted' ? 'selected' : '' }}>Restricted Access (No Downloads)</option>
                        </select>
                        @error('download_permission') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Abstract -->
                <div>
                    <label for="abstract" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Abstract *</label>
                    <textarea name="abstract" id="abstract" rows="5" required placeholder="Provide a detailed summary of your research methods, key findings, and scientific contribution..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">{{ old('abstract') }}</textarea>
                    @error('abstract') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Keywords & DOI -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="keywords" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Keywords (comma-separated)</label>
                        <input type="text" name="keywords" id="keywords" value="{{ old('keywords') }}" placeholder="Machine Learning, Neural Networks, Chemistry" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('keywords') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="doi" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">DOI (Digital Object Identifier)</label>
                        <input type="text" name="doi" id="doi" value="{{ old('doi') }}" placeholder="10.1000/xyz123" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('doi') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Publication Date & Copyright -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="publication_date" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Publication Date</label>
                        <input type="date" name="publication_date" id="publication_date" value="{{ old('publication_date') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('publication_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="copyright_information" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Copyright Statement</label>
                        <input type="text" name="copyright_information" id="copyright_information" value="{{ old('copyright_information') }}" placeholder="Creative Commons Attribution 4.0 International" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('copyright_information') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- File Uploads Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <!-- PDF Upload (Up to 100MB) -->
                    <div>
                        <label for="pdf_file" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">PDF Research Document *</label>
                        <input type="file" name="pdf_file" id="pdf_file" required accept="application/pdf" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white">
                        <p class="text-[10px] text-slate-400 mt-1">Strict PDF format (.pdf) up to <strong>256MB</strong></p>
                        @error('pdf_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Thumbnail Upload (Up to 5MB) -->
                    <div>
                        <label for="thumbnail_file" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Thumbnail Cover Image</label>
                        <input type="file" name="thumbnail_file" id="thumbnail_file" accept="image/jpeg,image/png,image/webp" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-700 dark:file:bg-slate-700 dark:file:text-white">
                        <p class="text-[10px] text-slate-400 mt-1">PNG, JPG, WEBP cover up to <strong>5MB</strong></p>
                        @error('thumbnail_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit & Draft Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <x-ui.button type="button" variant="outline" size="md" onclick="window.location.href='{{ route('dashboard.research.index') }}'">
                        Cancel
                    </x-ui.button>
                    <button type="submit" name="submit_action" value="draft" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition-colors">
                        Save as Draft
                    </button>
                    <button type="submit" name="submit_action" value="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-colors">
                        Submit for Moderation
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('uploadForm');
        if (!form) return;
        
        const pdfInput = document.getElementById('pdf_file');
        const thumbInput = document.getElementById('thumbnail_file');
        const submitBtns = form.querySelectorAll('button[type="submit"]');
        let isSubmitting = false;

        function formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        }

        function handleFileInput(input, maxSizeMB, errorMsg) {
            if (!input) return;
            input.addEventListener('change', function () {
                const file = this.files[0];
                
                const existingLabel = this.parentNode.querySelector('.file-size-label');
                if (existingLabel) existingLabel.remove();

                if (file) {
                    const maxBytes = maxSizeMB * 1024 * 1024;
                    if (file.size > maxBytes) {
                        alert(errorMsg);
                        this.value = '';
                        return;
                    }
                    
                    const sizeLabel = document.createElement('p');
                    sizeLabel.className = 'text-xs font-semibold text-blue-600 dark:text-blue-400 mt-2 file-size-label';
                    sizeLabel.textContent = 'Selected size: ' + formatBytes(file.size);
                    this.parentNode.appendChild(sizeLabel);
                }
            });
        }

        handleFileInput(pdfInput, 256, 'The selected PDF exceeds the maximum allowed size of 256MB.');
        handleFileInput(thumbInput, 5, 'The selected image exceeds the maximum allowed size of 5MB.');

        // Prevent double submit and show loader
        form.addEventListener('submit', function (e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            
            // Check required file again just in case
            if (pdfInput && pdfInput.files.length > 0 && pdfInput.files[0].size > (256 * 1024 * 1024)) {
                e.preventDefault();
                alert('The selected PDF exceeds the maximum allowed size of 256MB.');
                return;
            }

            isSubmitting = true;

            const submitValue = e.submitter ? e.submitter.value : 'submit';

            submitBtns.forEach(btn => {
                btn.style.pointerEvents = 'none';
                btn.classList.add('opacity-50');
                
                if (btn.value === submitValue) {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading research...`;
                }
            });

            // Show a waiting message below the buttons
            const actionsContainer = form.querySelector('.flex.flex-col.sm\\:flex-row.items-center.justify-end');
            if (actionsContainer && !document.getElementById('uploadWaitMsg')) {
                const msgContainer = document.createElement('div');
                msgContainer.id = 'uploadWaitMsg';
                msgContainer.className = 'w-full mt-4 text-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-100 dark:border-blue-800/50';
                msgContainer.innerHTML = '<span class="font-semibold block text-sm">Please wait while the file is being uploaded.</span><span class="text-xs opacity-80 block mt-1">Large files may take a few minutes. Do not refresh or close the page.</span>';
                actionsContainer.parentNode.appendChild(msgContainer);
            }
        });
    });
    </script>
    @endpush
</x-app-layout>
