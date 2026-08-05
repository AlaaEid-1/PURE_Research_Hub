<footer class="border-t border-slate-200 dark:border-slate-800/80 bg-white dark:bg-slate-900 transition-colors duration-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand Info -->
            <div class="md:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-base shadow-md shadow-blue-600/30">
                        P
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">PURE Research Hub</span>
                </a>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md leading-relaxed">
                    The modern, open platform for academic discovery, peer review collaboration, and research publication showcase. Connecting researchers worldwide.
                </p>
                <div class="flex items-center gap-4 text-slate-400">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        Open Access Ready
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400">
                        Academic Standard
                    </span>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-xs font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Navigation</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Home Page</a></li>
                    <li><a href="{{ route('about') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About PURE</a></li>
                    <li><a href="{{ route('contact') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Contact Support</a></li>
                    <li><a href="{{ route('login') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Researcher Sign In</a></li>
                </ul>
            </div>

            <!-- Attribution (Replaces Platform) -->
            <div>
                <h4 class="text-xs font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Attribution</h4>
                <div class="flex flex-col gap-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        👩‍💻 Coded by Aniqa Afzal on 02-22-2022 via SheCodes and open sourced.
                    </p>
                    
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                            Updated and Revised by Alaa Eid
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Laravel Developer | AI &amp; Research Technology
                        </p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">
                            PURE Research Hub (2026)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800/60 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} PURE Research Hub. All rights reserved. Professional Academic Publication Platform.
            </p>
            <div class="flex items-center gap-6 text-xs text-slate-400 dark:text-slate-500">
                <span>Privacy Policy</span>
                <span>Terms of Service</span>
                <span>Academic Guidelines</span>
            </div>
        </div>
    </div>
</footer>
