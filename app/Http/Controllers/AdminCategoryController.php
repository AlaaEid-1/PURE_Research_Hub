<?php

namespace App\Http\Controllers;

use App\Models\ResearchCategory;
use App\Services\ResearchCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function __construct(
        protected ResearchCategoryService $categoryService,
    ) {}

    public function index(): View
    {
        $categories = ResearchCategory::withCount('researches')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:research_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        ResearchCategory::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(ResearchCategory $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, ResearchCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:research_categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(ResearchCategory $category): RedirectResponse
    {
        // Prevent deletion if papers are attached
        if ($category->researches()->exists()) {
            return back()->with('error', 'Cannot delete a category that has research papers assigned to it.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }
}
