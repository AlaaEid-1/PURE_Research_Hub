<?php

namespace App\Http\Controllers;

use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Services\ResearchCategoryService;
use App\Services\ResearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminResearchController extends Controller
{
    public function __construct(
        protected ResearchService $researchService,
        protected ResearchCategoryService $categoryService,
    ) {}

    /**
     * Display paper moderation queue for administrators.
     */
    public function index(Request $request): View
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized access to administrator moderation queue.');
        }

        $status = $request->query('status');

        $query = Research::with(['user', 'category', 'authors'])->latest();

        if ($status && in_array($status, array_column(ResearchStatus::cases(), 'value'))) {
            $query->where('status', $status);
        }

        $researches = $query->paginate(15)->withQueryString();

        return view('admin.research.index', compact('researches', 'status'));
    }

    /**
     * Approve a research publication and invalidate category cache.
     */
    public function approve(Research $research): RedirectResponse
    {
        if (! request()->user()->isAdmin()) {
            abort(403);
        }

        Gate::authorize('update', $research);

        $research->update([
            'status' => ResearchStatus::PUBLISHED,
        ]);

        $this->categoryService->clearCache();

        return back()->with('success', 'Research publication approved and published.');
    }

    /**
     * Reject a research publication.
     */
    public function reject(Research $research): RedirectResponse
    {
        if (! request()->user()->isAdmin()) {
            abort(403);
        }

        Gate::authorize('update', $research);

        $research->update([
            'status' => ResearchStatus::REJECTED,
        ]);

        $this->categoryService->clearCache();

        return back()->with('success', 'Research publication rejected.');
    }

    /**
     * Mark a research publication as under review / changes requested.
     */
    public function requestChanges(Research $research): RedirectResponse
    {
        if (! request()->user()->isAdmin()) {
            abort(403);
        }

        Gate::authorize('update', $research);

        $research->update([
            'status' => ResearchStatus::UNDER_REVIEW,
        ]);

        return back()->with('success', 'Status updated to Under Review / Changes Requested.');
    }

    /**
     * Remove a research paper and invalidate category cache.
     */
    public function destroy(Research $research): RedirectResponse
    {
        if (! request()->user()->isAdmin()) {
            abort(403);
        }

        Gate::authorize('delete', $research);

        $this->researchService->deleteResearch($research);

        return back()->with('success', 'Research publication deleted by administrator.');
    }
}
