<?php

namespace App\Http\Controllers;

use App\Services\ResearchAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResearcherAnalyticsController extends Controller
{
    public function __construct(
        protected ResearchAnalyticsService $analyticsService
    ) {}

    /**
     * Display personal research analytics dashboard for researcher.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $stats = $this->analyticsService->getResearchStats($user);
        $impact = $this->analyticsService->getAuthorImpact($user);
        $downloadGrowth = $this->analyticsService->getDownloadGrowth($user);

        return view('dashboard.analytics', compact('stats', 'impact', 'downloadGrowth'));
    }
}
