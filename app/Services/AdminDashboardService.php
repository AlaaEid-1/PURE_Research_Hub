<?php

namespace App\Services;

use App\Enums\ResearchStatus;
use App\Enums\Role;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\ResearchDownloadLog;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminDashboardService
{
    /**
     * Get aggregate platform metrics for the admin dashboard.
     *
     * @return array<string, mixed>
     */
    public function getMetrics(): array
    {
        return [
            'total_researchers' => User::where('role', Role::USER)->count(),
            'total_publications' => Research::count(),
            'published_papers' => Research::where('status', ResearchStatus::PUBLISHED)->count(),
            'pending_reviews' => Research::where('status', ResearchStatus::PENDING)->count(),
            'total_downloads' => (int) Research::sum('downloads'),
            'total_views' => (int) Research::sum('views'),
        ];
    }

    /**
     * Get top downloaded research papers.
     */
    public function getMostDownloadedResearches(int $limit = 5): mixed
    {
        return Research::with(['user', 'category'])
            ->where('status', ResearchStatus::PUBLISHED)
            ->orderBy('downloads', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get most active researchers by publication volume.
     */
    public function getMostActiveResearchers(int $limit = 5): mixed
    {
        return User::withCount(['researches' => function ($q) {
            $q->where('status', ResearchStatus::PUBLISHED);
        }])
            ->where('role', Role::USER)
            ->orderBy('researches_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Chart-ready data for monthly publication growth over the last 6 months.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getMonthlyPublicationGrowth(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = Research::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Chart-ready data for monthly download growth over the last 6 months.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getMonthlyDownloadGrowth(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = ResearchDownloadLog::whereYear('downloaded_at', $month->year)
                ->whereMonth('downloaded_at', $month->month)
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Chart-ready data for research categories distribution.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getCategoryDistribution(): array
    {
        $categories = ResearchCategory::withCount(['researches' => function ($q) {
            $q->where('status', ResearchStatus::PUBLISHED);
        }])->orderBy('name')->get();

        return [
            'labels' => $categories->pluck('name')->toArray(),
            'data' => $categories->pluck('researches_count')->toArray(),
        ];
    }
}
