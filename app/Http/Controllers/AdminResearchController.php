<?php

namespace App\Http\Controllers;

use App\DTOs\ResearchData;
use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\ResearchCategory;
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
        $status = $request->query('status');

        $query = Research::with(['user', 'category'])->latest();

        if ($status && in_array($status, array_column(ResearchStatus::cases(), 'value'))) {
            $query->where('status', $status);
        }

        $researches = $query->paginate(20)->withQueryString();

        return view('admin.research.index', compact('researches', 'status'));
    }

    /**
     * Show research detail for admin review.
     */
    public function show(Research $research): View
    {
        $research->load(['user', 'category', 'accessRequests.requester']);

        return view('admin.research.show', compact('research'));
    }

    /**
     * Show edit form for research metadata.
     */
    public function edit(Research $research): View
    {
        Gate::authorize('update', $research);

        $categories = ResearchCategory::orderBy('name')->get();

        return view('admin.research.edit', compact('research', 'categories'));
    }

    /**
     * Update research metadata.
     */
    public function update(Request $request, Research $research): RedirectResponse
    {
        Gate::authorize('update', $research);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:500'],
            'abstract'    => ['required', 'string'],
            'keywords'    => ['nullable', 'string', 'max:1000'],
            'doi'         => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:research_categories,id'],
            'status'      => ['required', 'in:' . implode(',', array_column(ResearchStatus::cases(), 'value'))],
            'download_permission' => ['required', 'in:' . implode(',', array_column(DownloadPermission::cases(), 'value'))],
        ]);

        $research->update($validated);

        return redirect()->route('admin.research.show', $research)
            ->with('success', 'Research metadata updated.');
    }

    /**
     * Approve a research publication and notify author.
     */
    public function approve(Research $research): RedirectResponse
    {
        Gate::authorize('update', $research);

        $this->researchService->approveResearch($research);

        return back()->with('success', 'Research publication approved and published.');
    }

    /**
     * Reject a research publication and notify author.
     */
    public function reject(Research $research): RedirectResponse
    {
        Gate::authorize('update', $research);

        $this->researchService->rejectResearch($research);

        return back()->with('success', 'Research publication rejected.');
    }

    /**
     * Mark a research publication as under review / changes requested.
     */
    public function requestChanges(Research $research): RedirectResponse
    {
        Gate::authorize('update', $research);

        $this->researchService->requestChangesResearch($research);

        return back()->with('success', 'Status updated to Under Review.');
    }

    /**
     * Archive a research publication.
     */
    public function archive(Research $research): RedirectResponse
    {
        Gate::authorize('update', $research);

        $this->researchService->archiveResearch($research);

        return back()->with('success', 'Research publication archived.');
    }

    /**
     * Remove a research paper permanently.
     */
    public function destroy(Research $research): RedirectResponse
    {
        Gate::authorize('delete', $research);

        $this->researchService->deleteResearch($research);

        return redirect()->route('admin.research.index')
            ->with('success', 'Research publication deleted.');
    }
}
