<?php

namespace App\Http\Controllers;

use App\DTOs\ResearchData;
use App\Http\Requests\StoreResearchRequest;
use App\Http\Requests\UpdateResearchRequest;
use App\Models\Research;
use App\Services\ResearchCategoryService;
use App\Services\ResearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardResearchController extends Controller
{
    public function __construct(
        protected ResearchService $researchService,
        protected ResearchCategoryService $categoryService,
    ) {}

    /**
     * Display a listing of the researcher's publications.
     */
    public function index(Request $request): View
    {
        $researches = Research::with(['category', 'authors'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('dashboard.research.index', compact('researches'));
    }

    /**
     * Show the form for creating a new research paper.
     */
    public function create(): View
    {
        $categories = $this->categoryService->getAllCached();

        return view('dashboard.research.create', compact('categories'));
    }

    /**
     * Store a newly created research paper in storage.
     */
    public function store(StoreResearchRequest $request): RedirectResponse
    {
        $dto = ResearchData::fromArray($request->validated());

        $this->researchService->createResearch($request->user(), $dto);

        return redirect()->route('dashboard.research.index')
            ->with('success', 'Research paper published successfully!');
    }

    /**
     * Show the form for editing the specified research paper.
     */
    public function edit(Research $research): View
    {
        Gate::authorize('update', $research);

        $categories = $this->categoryService->getAllCached();

        return view('dashboard.research.edit', compact('research', 'categories'));
    }

    /**
     * Update the specified research paper in storage.
     */
    public function update(UpdateResearchRequest $request, Research $research): RedirectResponse
    {
        Gate::authorize('update', $research);

        $dto = ResearchData::fromArray($request->validated());

        $this->researchService->updateResearch($research, $dto);

        return redirect()->route('dashboard.research.index')
            ->with('success', 'Research paper updated successfully!');
    }

    /**
     * Remove the specified research paper from storage.
     */
    public function destroy(Research $research): RedirectResponse
    {
        Gate::authorize('delete', $research);

        $this->researchService->deleteResearch($research);

        return redirect()->route('dashboard.research.index')
            ->with('success', 'Research paper deleted successfully.');
    }
}
