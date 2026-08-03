<x-guest-layout title="Register Academic Account - PURE Research Hub">
    <div class="py-12 sm:py-20 max-w-lg mx-auto px-4">
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-600/30 mx-auto mb-4">
                    P
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Join PURE Academic Network</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Create your researcher profile & publish account</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        autofocus
                        value="{{ old('name') }}" 
                        placeholder="Dr. Jane Doe"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                    @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Institutional / Academic Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        value="{{ old('email') }}" 
                        placeholder="j.doe@university.edu"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="institution" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">University / Institution</label>
                        <input 
                            type="text" 
                            name="institution" 
                            id="institution" 
                            value="{{ old('institution') }}" 
                            placeholder="Oxford University"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                        @error('institution') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="department" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Department</label>
                        <input 
                            type="text" 
                            name="department" 
                            id="department" 
                            value="{{ old('department') }}" 
                            placeholder="Computer Science"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                        @error('department') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                    @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Confirm Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                </div>

                <x-ui.button type="submit" variant="primary" size="lg" class="w-full py-3">
                    Create Researcher Account
                </x-ui.button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Already registered? 
                    <a href="{{ route('login') }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
