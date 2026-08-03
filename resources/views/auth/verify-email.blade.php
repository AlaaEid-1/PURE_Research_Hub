<x-guest-layout title="Verify Email - PURE Research Hub">
    <div class="py-12 sm:py-20 max-w-md mx-auto px-4">
        <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-xl glass-card text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-600/10 text-blue-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Verify Your Email</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Before proceeding, please verify your email address by clicking on the link we just emailed to you.
            </p>

            <div class="mt-6 space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-ui.button type="submit" variant="primary" size="md" class="w-full">
                        Resend Verification Email
                    </x-ui.button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
