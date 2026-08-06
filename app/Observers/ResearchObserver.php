<?php

namespace App\Observers;

use App\Models\Research;
use App\Services\ResearchCategoryService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResearchObserver
{
    public function __construct(
        protected ResearchCategoryService $categoryService
    ) {}

    /**
     * Handle the Research "creating" event.
     */
    public function creating(Research $research): void
    {
        if (empty($research->slug)) {
            $research->slug = $this->generateUniqueSlug($research->title);
        }
    }

    /**
     * Handle the Research "created" event — invalidate category cache.
     */
    public function created(Research $research): void
    {
        $this->categoryService->clearCache();
    }

    /**
     * Handle the Research "updating" event.
     */
    public function updating(Research $research): void
    {
        if ($research->isDirty('title') && ! $research->isDirty('slug')) {
            $research->slug = $this->generateUniqueSlug($research->title, $research->id);
        }

        // Clean up old PDF if replaced
        if ($research->isDirty('pdf_path')) {
            $oldPdf = $research->getOriginal('pdf_path');
            if ($oldPdf && Storage::disk('private_research')->exists($oldPdf)) {
                Storage::disk('private_research')->delete($oldPdf);
            }
        }

        // Clean up old thumbnail if replaced
        if ($research->isDirty('thumbnail_path')) {
            $oldThumb = $research->getOriginal('thumbnail_path');
            if ($oldThumb && Storage::disk('public')->exists($oldThumb)) {
                Storage::disk('public')->delete($oldThumb);
            }
        }
    }

    /**
     * Handle the Research "deleted" event — cleanup files and invalidate category cache.
     */
    public function deleted(Research $research): void
    {
        if ($research->pdf_path && Storage::disk('private_research')->exists($research->pdf_path)) {
            Storage::disk('private_research')->delete($research->pdf_path);
        }

        if ($research->thumbnail_path && Storage::disk('public')->exists($research->thumbnail_path)) {
            Storage::disk('public')->delete($research->thumbnail_path);
        }

        $this->categoryService->clearCache();
    }

    /**
     * Generate a unique slug for the research title.
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (Research::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
