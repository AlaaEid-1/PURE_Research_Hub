<x-app-layout title="Profile Settings - PURE Research Hub">
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Academic Profile & Account Settings</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage your public researcher information, institutional affiliation, academic identifiers, and security.</p>
        </div>

        <!-- Section 1: Profile Information -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Researcher Profile Details</h2>

            <form method="POST" action="{{ route('user-profile-information.update') }}" id="profileForm" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Avatar Upload Field -->
                <div class="flex items-center gap-6">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-blue-500/20 shadow-md" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                    <div>
                        <label for="avatar" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-1">Avatar Image</label>
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950 dark:file:text-blue-300">
                        <p class="text-[10px] text-slate-400 mt-1">PNG, JPG, WEBP up to 5MB</p>
                        @error('avatar', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('name', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('email', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="institution" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">University / Institution</label>
                        <input type="text" name="institution" id="institution" value="{{ old('institution', $user->institution) }}" placeholder="e.g. Stanford University" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('institution', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="department" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Department / Field</label>
                        <input type="text" name="department" id="department" value="{{ old('department', $user->department) }}" placeholder="e.g. Department of Computer Science" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('department', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Academic Identifiers & External Links -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label for="orcid_id" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">ORCID iD</label>
                        <input type="text" name="orcid_id" id="orcid_id" value="{{ old('orcid_id', $user->orcid_id) }}" placeholder="0000-0002-1825-0097" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('orcid_id', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="google_scholar_url" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Google Scholar URL</label>
                        <input type="url" name="google_scholar_url" id="google_scholar_url" value="{{ old('google_scholar_url', $user->google_scholar_url) }}" placeholder="https://scholar.google.com/citations?user=..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('google_scholar_url', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="website_url" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Personal Website</label>
                        <input type="url" name="website_url" id="website_url" value="{{ old('website_url', $user->website_url) }}" placeholder="https://myresearchlab.edu" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('website_url', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="research_interests" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Research Interests (Comma Separated)</label>
                    <input type="text" name="research_interests" id="research_interests" value="{{ old('research_interests', $user->research_interests) }}" placeholder="Machine Learning, Genomics, Quantum Computing, Cryptography" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @error('research_interests', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="bio" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Academic Biography</label>
                    <textarea name="bio" id="bio" rows="4" placeholder="Brief description of your research interests and academic background..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio', 'updateProfileInformation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-colors">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 2: Password Update -->
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Update Password</h2>

            <form method="POST" action="{{ route('user-password.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @error('current_password', 'updatePassword') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">New Password</label>
                        <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('password', 'updatePassword') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="secondary" size="md">
                        Update Password
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('profileForm');
        if (!form) return;
        
        const avatarInput = document.getElementById('avatar');
        const submitBtn = form.querySelector('button[type="submit"]');
        let isSubmitting = false;

        function formatBytes(bytes, decimals = 2) {
            if (!+bytes) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
        }

        if (avatarInput) {
            avatarInput.addEventListener('change', function () {
                const file = this.files[0];
                
                const existingLabel = this.parentNode.querySelector('.file-size-label');
                if (existingLabel) existingLabel.remove();

                if (file) {
                    const maxBytes = 5 * 1024 * 1024;
                    if (file.size > maxBytes) {
                        alert('The selected image exceeds the maximum allowed size of 5MB.');
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

        form.addEventListener('submit', function (e) {
            if (isSubmitting) {
                e.preventDefault();
                return;
            }
            
            if (avatarInput && avatarInput.files.length > 0 && avatarInput.files[0].size > (5 * 1024 * 1024)) {
                e.preventDefault();
                alert('The selected image exceeds the maximum allowed size of 5MB.');
                return;
            }

            isSubmitting = true;

            if (submitBtn) {
                submitBtn.style.pointerEvents = 'none';
                submitBtn.classList.add('opacity-50');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
            }
        });
    });
    </script>
    @endpush
</x-app-layout>
