<x-guest-layout title="Forgot Password - PURE Research Hub">
    <div class="py-12 sm:py-20 max-w-md mx-auto px-4">
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card">
            <div class="text-center mb-8">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-600/30 mx-auto mb-4">
                    P
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reset Password</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Forgot your password? Enter your email address and we will send you a password reset link.
                </p>
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        autofocus
                        value="{{ old('email') }}" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <x-ui.button type="submit" variant="primary" size="lg" class="w-full py-3">
                    Send Password Reset Link
                </x-ui.button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400">
                    &larr; Back to Login
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
