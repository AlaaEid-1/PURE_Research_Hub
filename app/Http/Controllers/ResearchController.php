<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Services\ResearchCategoryService;
use App\Services\ResearchSearchService;
use App\Services\ResearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResearchController extends Controller
{
    public function __construct(
        protected ResearchSearchService $searchService,
        protected ResearchService $researchService,
        protected ResearchCategoryService $categoryService
    ) {}

    /**
     * Display a listing of published research papers.
     */
    public function index(Request $request): View
    {
        $categories = $this->categoryService->getAllCached();
        $researches = $this->searchService->searchPublished($request);

        return view('research.index', compact('researches', 'categories'));
    }

    /**
     * Display the specified research paper publication page.
     */
    public function show(string $slug): View
    {
        $research = Research::with(['user', 'category', 'authors'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->researchService->incrementViews($research);

        $relatedResearch = $this->searchService->getRelatedResearches($research);

        return view('research.show', compact('research', 'relatedResearch'));
    }
}
