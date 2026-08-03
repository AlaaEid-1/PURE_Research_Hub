<x-admin-layout title="Admin Analytics & Platform Intelligence">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Admin Control & Analytics Dashboard</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Platform metrics, publication growth, and content moderation control.</p>
            </div>

            <a href="{{ route('admin.research.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md transition-colors">
                Manage Moderation Queue &rarr;
            </a>
        </div>

        <!-- Metrics Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Researchers</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($metrics['total_researchers']) }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Papers</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($metrics['total_publications']) }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Published</p>
                <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($metrics['published_papers']) }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Pending Reviews</p>
                <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($metrics['pending_reviews']) }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Views</p>
                <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($metrics['total_views']) }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">PDF Downloads</p>
                <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($metrics['total_downloads']) }}</p>
            </div>
        </div>

        <!-- Chart.js Visualization Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Publications Growth Chart -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs">Publications Growth Trend</h3>
                <div class="h-64">
                    <canvas id="pubGrowthChart"></canvas>
                </div>
            </div>

            <!-- Category Distribution Chart -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs">Research Categories Distribution</h3>
                <div class="h-64">
                    <canvas id="catDistChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Tables Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Papers -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Most Downloaded Research</h3>
                <div class="space-y-3">
                    @foreach($mostDownloaded as $paper)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between gap-3 text-xs">
                            <div class="overflow-hidden">
                                <a href="{{ route('research.show', $paper->slug) }}" class="font-bold text-slate-900 dark:text-white hover:underline truncate block">
                                    {{ $paper->title }}
                                </a>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $paper->user->name }} &bull; {{ $paper->category->name ?? 'General' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 font-bold shrink-0">
                                {{ number_format($paper->downloads) }} DLs
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Active Researchers -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Most Active Researchers</h3>
                <div class="space-y-3">
                    @foreach($mostActive as $scholar)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <img src="{{ $scholar->avatar_url }}" alt="{{ $scholar->name }}" class="w-8 h-8 rounded-lg object-cover" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-fallback.svg') }}';">
                                <div>
                                    <a href="{{ route('researchers.show', $scholar) }}" class="font-bold text-slate-900 dark:text-white hover:underline">
                                        {{ $scholar->name }}
                                    </a>
                                    <p class="text-[10px] text-slate-400">{{ $scholar->institution ?? 'Academic Researcher' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400 font-bold shrink-0">
                                {{ number_format($scholar->researches_count) }} Papers
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Publication Growth Chart
            new Chart(document.getElementById('pubGrowthChart'), {
                type: 'line',
                data: {
                    labels: @json($pubGrowth['labels']),
                    datasets: [{
                        label: 'Publications',
                        data: @json($pubGrowth['data']),
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Category Distribution Chart
            new Chart(document.getElementById('catDistChart'), {
                type: 'bar',
                data: {
                    labels: @json($catDist['labels']),
                    datasets: [{
                        label: 'Papers Count',
                        data: @json($catDist['data']),
                        backgroundColor: '#06B6D4'
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        });
    </script>
</x-admin-layout>
