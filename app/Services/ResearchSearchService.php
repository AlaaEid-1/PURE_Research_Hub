<?php

namespace App\Services;

use App\Models\Research;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ResearchSearchService
{
    /**
     * Search and filter published research publications based on query parameters.
     */
    public function searchPublished(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $query = Research::with(['user', 'category', 'authors'])
            ->withCount('citations')
            ->where('status', \App\Enums\ResearchStatus::PUBLISHED);

        // Full text Keyword / Title / Abstract / Keywords / Author Search
        if ($request->filled('query')) {
            $searchTerm = trim((string) $request->input('query'));
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('abstract', 'like', "%{$searchTerm}%")
                    ->orWhere('keywords', 'like', "%{$searchTerm}%")
                    ->orWhere('doi', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($uq) use ($searchTerm) {
                        $uq->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('institution', 'like', "%{$searchTerm}%")
                            ->orWhere('department', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Specific DOI Search
        if ($request->filled('doi')) {
            $query->where('doi', 'like', '%'.trim((string) $request->input('doi')).'%');
        }

        // Author Institution Filter
        if ($request->filled('institution')) {
            $inst = trim((string) $request->input('institution'));
            $query->whereHas('user', fn ($uq) => $uq->where('institution', 'like', "%{$inst}%"));
        }

        // Category Filter (Single or Multiple Categories)
        if ($request->filled('category')) {
            $categorySlug = (string) $request->input('category');
            $query->whereHas('category', fn ($cq) => $cq->where('slug', $categorySlug));
        } elseif ($request->filled('categories') && is_array($request->input('categories'))) {
            $categorySlugs = (array) $request->input('categories');
            $query->whereHas('category', fn ($cq) => $cq->whereIn('slug', $categorySlugs));
        }

        // Date / Year Filter
        if ($request->filled('year')) {
            $year = (int) $request->input('year');
            $query->whereYear('publication_date', $year);
        }

        // Download Permission Filter
        if ($request->filled('permission')) {
            $query->where('download_permission', (string) $request->input('permission'));
        }

        // Sorting
        $sort = (string) $request->input('sort', 'newest');
        match ($sort) {
            'relevance' => $query->orderBy('views', 'desc')->orderBy('downloads', 'desc'),
            'most_viewed' => $query->orderBy('views', 'desc'),
            'most_downloaded' => $query->orderBy('downloads', 'desc'),
            'most_cited' => $query->orderBy('citations_count', 'desc'),
            'updated' => $query->orderBy('updated_at', 'desc'),
            default => $query->latest('publication_date')->latest('id'),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
