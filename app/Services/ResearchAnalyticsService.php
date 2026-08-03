<?php

namespace App\Services;

use App\Enums\AccessRequestStatus;
use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\ResearchAccessRequest;
use App\Models\ResearchDownloadLog;
use App\Models\User;
use Illuminate\Support\Carbon;

class ResearchAnalyticsService
{
    /**
     * Get aggregate research performance metrics for a specific researcher.
     *
     * @return array<string, mixed>
     */
    public function getResearchStats(User $user): array
    {
        $publishedQuery = Research::where('user_id', $user->id)->where('status', ResearchStatus::PUBLISHED);

        $totalViews = (int) $publishedQuery->sum('views');
        $totalDownloads = (int) $publishedQuery->sum('downloads');

        $totalRequests = ResearchAccessRequest::whereHas('research', fn ($q) => $q->where('user_id', $user->id))->count();
        $approvedRequests = ResearchAccessRequest::whereHas('research', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', AccessRequestStatus::APPROVED)
            ->count();

        $approvalRate = $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 100.0;

        $popularPapers = Research::where('user_id', $user->id)
            ->where('status', ResearchStatus::PUBLISHED)
            ->orderBy('downloads', 'desc')
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        return [
            'total_views' => $totalViews,
            'total_downloads' => $totalDownloads,
            'total_requests' => $totalRequests,
            'approved_requests' => $approvedRequests,
            'approval_rate' => $approvalRate,
            'popular_papers' => $popularPapers,
        ];
    }

    /**
     * Get author impact score statistics.
     *
     * @return array<string, mixed>
     */
    public function getAuthorImpact(User $user): array
    {
        $publishedCount = Research::where('user_id', $user->id)->where('status', ResearchStatus::PUBLISHED)->count();
        $totalCitations = Research::where('user_id', $user->id)->withCount('citations')->get()->sum('citations_count');

        return [
            'published_count' => $publishedCount,
            'total_citations' => $totalCitations,
            'avg_citations_per_paper' => $publishedCount > 0 ? round($totalCitations / $publishedCount, 2) : 0,
        ];
    }

    /**
     * Chart-ready data for researcher's monthly download growth.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getDownloadGrowth(User $user): array
    {
        $labels = [];
        $data = [];

        $userResearchIds = Research::where('user_id', $user->id)->pluck('id');

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $count = ResearchDownloadLog::whereIn('research_id', $userResearchIds)
                ->whereYear('downloaded_at', $month->year)
                ->whereMonth('downloaded_at', $month->month)
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
