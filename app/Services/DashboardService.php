<?php

namespace App\Services;

use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\ResearchAccessRequest;
use App\Models\User;

class DashboardService
{
    /**
     * Get aggregated statistics and metrics for the given user's dashboard.
     * Returns real publication counts per status, views, downloads, and pending requests.
     *
     * @return array<string, mixed>
     */
    public function getUserStats(User $user): array
    {
        // Single query with conditional aggregates for all status counts
        $publishedStats = $user->researches()
            ->where('status', ResearchStatus::PUBLISHED)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(views), 0) as views, COALESCE(SUM(downloads), 0) as downloads')
            ->first();

        $statusCounts = $user->researches()
            ->selectRaw('
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as under_review
            ', [
                ResearchStatus::PENDING->value,
                ResearchStatus::DRAFT->value,
                ResearchStatus::REJECTED->value,
                ResearchStatus::UNDER_REVIEW->value,
            ])
            ->first();

        // Count incoming pending access requests on the user's papers
        $pendingAccessRequests = ResearchAccessRequest::whereHas(
            'research',
            fn ($q) => $q->where('user_id', $user->id)
        )->where('status', 'pending')->count();

        $profileCompleteness = 40;
        if (! empty($user->institution)) {
            $profileCompleteness += 15;
        }
        if (! empty($user->department)) {
            $profileCompleteness += 15;
        }
        if (! empty($user->bio)) {
            $profileCompleteness += 10;
        }
        if (! empty($user->orcid_id)) {
            $profileCompleteness += 10;
        }
        if (! empty($user->research_interests)) {
            $profileCompleteness += 10;
        }

        return [
            'total_publications' => (int) ($publishedStats->count ?? 0),
            'total_views' => (int) ($publishedStats->views ?? 0),
            'total_downloads' => (int) ($publishedStats->downloads ?? 0),
            'pending_papers' => (int) ($statusCounts->pending ?? 0),
            'draft_papers' => (int) ($statusCounts->draft ?? 0),
            'rejected_papers' => (int) ($statusCounts->rejected ?? 0),
            'under_review_papers' => (int) ($statusCounts->under_review ?? 0),
            'pending_access_requests' => $pendingAccessRequests,
            'profile_completeness' => min(100, $profileCompleteness),
            'verified_status' => $user->is_verified_researcher ? 'Verified Academic' : 'Pending Verification',
            'recent_activity' => [
                [
                    'title' => 'Account Created',
                    'date' => $user->created_at ? $user->created_at->diffForHumans() : 'Recently',
                    'icon' => 'user-check',
                ],
            ],
        ];
    }

    /**
     * Get platform overview statistics for admin view or global metrics.
     *
     * @return array<string, mixed>
     */
    public function getAdminStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_researchers' => User::where('role', 'user')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_publications' => Research::where('status', ResearchStatus::PUBLISHED)->count(),
        ];
    }
}
