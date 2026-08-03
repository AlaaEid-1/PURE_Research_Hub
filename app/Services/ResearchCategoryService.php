<?php

namespace App\Services;

use App\Models\ResearchCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ResearchCategoryService
{
    /**
     * Cache key for active categories list.
     */
    public const CACHE_KEY = 'categories_all_cached';

    /**
     * Get all categories cached for 24 hours.
     *
     * @return Collection<int, ResearchCategory>
     */
    public function getAllCached(): Collection
    {
        try {
            $cached = Cache::get(self::CACHE_KEY);

            if ($cached instanceof Collection) {
                $hasIncomplete = $cached->contains(function ($item) {
                    return $item instanceof \__PHP_Incomplete_Class || (is_object($item) && str_contains(get_class($item), '__PHP_Incomplete_Class'));
                });

                if (! $hasIncomplete) {
                    return $cached;
                }
            }
        } catch (\Throwable $e) {
            // Ignore unserialization errors or other unexpected exceptions
        }

        // Cache is missing, invalid type, or __PHP_Incomplete_Class
        $this->clearCache();

        return Cache::remember(self::CACHE_KEY, now()->addDay(), function () {
            return ResearchCategory::withCount(['researches' => fn ($q) => $q->where('status', 'published')])
                ->orderBy('name', 'asc')
                ->get();
        });
    }

    /**
     * Flush categories cache on update/create.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
