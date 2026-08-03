<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ResearcherProfileController extends Controller
{
    /**
     * Display the specified public researcher profile.
     *
     * Uses a single aggregated query to retrieve views, downloads, and publication
     * count — eliminating the previous N+1 pattern of 3 separate queries.
     */
    public function show(User $user): View
    {
        $researches = $user->researches()
            ->with(['category', 'authors'])
            ->where('status', 'published')
            ->latest()
            ->paginate(10);

        // Single aggregated query instead of 3 separate queries
        $aggregate = $user->researches()
            ->where('status', 'published')
            ->selectRaw('COUNT(*) as total_publications, COALESCE(SUM(views), 0) as total_views, COALESCE(SUM(downloads), 0) as total_downloads')
            ->first();

        $stats = [
            'total_publications' => (int) ($aggregate->total_publications ?? 0),
            'total_views' => (int) ($aggregate->total_views ?? 0),
            'total_downloads' => (int) ($aggregate->total_downloads ?? 0),
        ];

        return view('researchers.show', compact('user', 'researches', 'stats'));
    }
}
