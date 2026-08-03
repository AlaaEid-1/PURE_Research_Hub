<?php

namespace App\Http\Controllers;

use App\Models\ResearchCategory;
use App\Services\ResearchCategoryService;
use Illuminate\View\View;

class ResearchCategoryController extends Controller
{
    public function __construct(
        protected ResearchCategoryService $categoryService
    ) {}

    /**
     * Display a listing of academic research categories.
     */
    public function index(): View
    {
        $categories = $this->categoryService->getAllCached();

        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified category and its published research papers.
     */
    public function show(string $slug): View
    {
        $category = ResearchCategory::where('slug', $slug)->firstOrFail();

        $researches = $category->researches()
            ->with(['user', 'category', 'authors'])
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('categories.show', compact('category', 'researches'));
    }
}
