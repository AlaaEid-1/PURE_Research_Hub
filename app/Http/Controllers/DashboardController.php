<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected AdminDashboardService $adminDashboardService
    ) {}

    /**
     * Display user dashboard with aggregated statistics.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $stats = $this->dashboardService->getUserStats($user);

        return view('dashboard.index', compact('user', 'stats'));
    }

    /**
     * Display admin dashboard portal with analytics and charts.
     */
    public function admin(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized access to administrator dashboard.');
        }

        $metrics = $this->adminDashboardService->getMetrics();
        $mostDownloaded = $this->adminDashboardService->getMostDownloadedResearches();
        $mostActive = $this->adminDashboardService->getMostActiveResearchers();
        $pubGrowth = $this->adminDashboardService->getMonthlyPublicationGrowth();
        $dlGrowth = $this->adminDashboardService->getMonthlyDownloadGrowth();
        $catDist = $this->adminDashboardService->getCategoryDistribution();

        return view('dashboard.admin', compact(
            'metrics',
            'mostDownloaded',
            'mostActive',
            'pubGrowth',
            'dlGrowth',
            'catDist'
        ));
    }
}
