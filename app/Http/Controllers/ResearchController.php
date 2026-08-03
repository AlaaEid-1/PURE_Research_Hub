<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Services\ResearchSearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResearchController extends Controller
{
    public function __construct(
        protected ResearchSearchService $searchService
    ) {}

    /**
     * Display a listing of published research papers.
     */
    public function index(Request $request): View
    {
        $categories = ResearchCategory::orderBy('name')->get();
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

        $research->increment('views');

        // Fetch related research using priority ranking
        $query = Research::with(['user', 'category'])
            ->where('id', '!=', $research->id)
            ->where('status', \App\Enums\ResearchStatus::PUBLISHED)
            ->where('download_permission', '!=', \App\Enums\DownloadPermission::RESTRICTED) // Skip restricted from public suggestions
            ->where(function ($q) use ($research) {
                $q->where('category_id', $research->category_id)
                    ->orWhere('user_id', $research->user_id);

                if ($research->keywords) {
                    $keywords = array_filter(array_map('trim', explode(',', $research->keywords)));
                    foreach ($keywords as $keyword) {
                        $q->orWhere('keywords', 'like', '%'.$keyword.'%');
                    }
                }
            })
            ->latest('views')
            ->take(20)
            ->get();

        $relatedResearch = $query->sortByDesc(function ($item) use ($research) {
            $score = 0;
            if ($item->category_id === $research->category_id) {
                $score += 10;
            }

            if ($research->keywords && $item->keywords) {
                $k1 = array_filter(array_map('trim', explode(',', strtolower($research->keywords))));
                $k2 = array_filter(array_map('trim', explode(',', strtolower($item->keywords))));
                $score += count(array_intersect($k1, $k2)) * 5;
            }

            if ($item->user_id === $research->user_id) {
                $score += 2;
            }

            return $score;
        })->take(4)->values();

        return view('research.show', compact('research', 'relatedResearch'));
    }
}
