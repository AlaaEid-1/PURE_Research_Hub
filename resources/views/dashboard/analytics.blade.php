<x-app-layout title="Researcher Intelligence & Analytics - PURE Research Hub">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-8">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Researcher Intelligence & Impact Analytics</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Track reading engagement, citation impact, download growth, and access request metrics.</p>
        </div>

        <!-- Metrics Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Views</p>
                <p class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['total_views']) }}</p>
            </div>

            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">PDF Downloads</p>
                <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['total_downloads']) }}</p>
            </div>

            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Access Requests</p>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($stats['total_requests']) }}</p>
            </div>

            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Approval Rate</p>
                <p class="text-3xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['approval_rate'] }}%</p>
            </div>
        </div>

        <!-- Citation Impact & Download Trend Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Author Impact Summary -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card space-y-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs">Scholarly Impact</h3>

                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs text-slate-500">Total Publications</p>
                        <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ number_format($impact['published_count']) }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs text-slate-500">Total Citations</p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ number_format($impact['total_citations']) }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-xs text-slate-500">Avg Citations / Paper</p>
                        <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $impact['avg_citations_per_paper'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Download Growth Chart -->
            <div class="lg:col-span-2 p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs">Monthly Download Trend</h3>
                <div class="h-64">
                    <canvas id="researcherDlGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Papers List -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-md glass-card space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Most Popular Publications</h3>

            @if($stats['popular_papers']->count() > 0)
                <div class="space-y-3">
                    @foreach($stats['popular_papers'] as $paper)
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between gap-4 text-xs">
                            <div>
                                <a href="{{ route('research.show', $paper->slug) }}" class="font-bold text-slate-900 dark:text-white hover:underline text-sm block">
                                    {{ $paper->title }}
                                </a>
                                <p class="text-[11px] text-slate-500 mt-1">{{ $paper->category->name ?? 'General Science' }} &bull; Published {{ $paper->created_at->format('M Y') }}</p>
                            </div>

                            <div class="flex items-center gap-4 text-slate-500 shrink-0">
                                <span class="flex items-center gap-1 font-semibold text-blue-600 dark:text-blue-400">
                                    {{ number_format($paper->views) }} Views
                                </span>
                                <span class="flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($paper->downloads) }} Downloads
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 italic">No published papers available for analytics calculation.</p>
            @endif
        </div>
    </div>

    <!-- Chart.js Script Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Chart(document.getElementById('researcherDlGrowthChart'), {
                type: 'line',
                data: {
                    labels: @json($downloadGrowth['labels']),
                    datasets: [{
                        label: 'PDF Downloads',
                        data: @json($downloadGrowth['data']),
                        borderColor: '#06B6D4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        });
    </script>
</x-app-layout>
