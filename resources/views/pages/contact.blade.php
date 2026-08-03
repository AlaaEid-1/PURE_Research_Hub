<x-guest-layout title="Contact Us - PURE Research Hub">
    <div class="py-12 lg:py-20 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Contact Academic Support</h1>
            <p class="mt-2 text-slate-600 dark:text-slate-400">Have questions about joining PURE Research Hub or institutional partnerships?</p>
        </div>

        <div class="p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-lg glass-card">
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Your Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Subject</label>
                    <input type="text" name="subject" id="subject" required value="{{ old('subject') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @error('subject') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="message" class="block text-xs font-semibold uppercase text-slate-700 dark:text-slate-300 mb-2">Message</label>
                    <textarea name="message" id="message" rows="5" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">{{ old('message') }}</textarea>
                    @error('message') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="primary" size="lg">
                        Send Message
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
