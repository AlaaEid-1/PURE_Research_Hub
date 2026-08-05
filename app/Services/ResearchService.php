<?php

namespace App\Services;

use App\DTOs\ResearchData;
use App\Enums\ResearchStatus;
use App\Jobs\ProcessResearchPdfJob;
use App\Models\Research;
use App\Models\User;
use App\Notifications\ResearchStatusChangedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;

class ResearchService
{
    /**
     * Create a new research publication record and handle PDF upload.
     */
    public function createResearch(User $user, ResearchData $dto): Research
    {
        $pdfPath = null;
        if ($dto->pdfFile) {
            $magic = file_get_contents($dto->pdfFile->getRealPath(), false, null, 0, 4);
            if ($magic !== '%PDF') {
                throw ValidationException::withMessages(['pdf_file' => 'The uploaded file is not a valid PDF document.']);
            }

            $safeFilename = Str::uuid().'.pdf';
            $pdfPath = $dto->pdfFile->storeAs('research_pdfs', $safeFilename, 'private_research');
        }

        $thumbnailPath = null;
        if ($dto->thumbnailFile) {
            $thumbnailPath = $this->processThumbnail($dto->thumbnailFile);
        }

        $research = Research::create([
            'user_id' => $user->id,
            'category_id' => $dto->categoryId,
            'title' => $dto->title,
            'abstract' => $dto->abstract,
            'keywords' => $dto->keywords,
            'doi' => $dto->doi,
            'publication_date' => $dto->publicationDate,
            'pdf_path' => $pdfPath ?? '',
            'thumbnail_path' => $thumbnailPath,
            'copyright_information' => $dto->copyrightInformation,
            'download_permission' => $dto->downloadPermission,
            'status' => $dto->status,
            'views' => 0,
            'downloads' => 0,
        ]);

        $this->syncAuthors($research, $user, $dto->coAuthorIds);

        if ($pdfPath) {
            ProcessResearchPdfJob::dispatch($pdfPath);
        }

        return $research;
    }

    /**
     * Update an existing research publication.
     */
    public function updateResearch(Research $research, ResearchData $dto): Research
    {
        $pdfPath = $research->pdf_path;
        $newPdfUploaded = false;

        if ($dto->pdfFile) {
            $magic = file_get_contents($dto->pdfFile->getRealPath(), false, null, 0, 4);
            if ($magic !== '%PDF') {
                throw ValidationException::withMessages(['pdf_file' => 'The uploaded replacement file is not a valid PDF document.']);
            }

            $safeFilename = Str::uuid().'.pdf';
            $pdfPath = $dto->pdfFile->storeAs('research_pdfs', $safeFilename, 'private_research');
            $newPdfUploaded = true;
        }

        $thumbnailPath = $research->thumbnail_path;
        if ($dto->thumbnailFile) {
            $thumbnailPath = $this->processThumbnail($dto->thumbnailFile);
        }

        $research->update([
            'category_id' => $dto->categoryId,
            'title' => $dto->title,
            'abstract' => $dto->abstract,
            'keywords' => $dto->keywords,
            'doi' => $dto->doi,
            'publication_date' => $dto->publicationDate,
            'pdf_path' => $pdfPath,
            'thumbnail_path' => $thumbnailPath,
            'copyright_information' => $dto->copyrightInformation,
            'download_permission' => $dto->downloadPermission,
            'status' => $dto->status,
        ]);

        $this->syncAuthors($research, $research->user, $dto->coAuthorIds);

        if ($newPdfUploaded && $pdfPath) {
            ProcessResearchPdfJob::dispatch($pdfPath);
        }

        return $research;
    }

    /**
     * Delete a research publication and clean up files.
     */
    public function deleteResearch(Research $research): void
    {
        $research->delete();
    }

    /**
     * Approve a research publication and send notification to author.
     */
    public function approveResearch(Research $research): void
    {
        $this->updateStatus($research, ResearchStatus::PUBLISHED);
    }

    /**
     * Reject a research publication and send notification to author.
     */
    public function rejectResearch(Research $research): void
    {
        $this->updateStatus($research, ResearchStatus::REJECTED);
    }

    /**
     * Request changes for a research publication and send notification to author.
     */
    public function requestChangesResearch(Research $research): void
    {
        $this->updateStatus($research, ResearchStatus::UNDER_REVIEW);
    }

    /**
     * Archive a research publication.
     */
    public function archiveResearch(Research $research): void
    {
        $this->updateStatus($research, ResearchStatus::ARCHIVED);
    }


    /**
     * Update paper status, clear category cache, and dispatch author notification.
     */
    public function updateStatus(Research $research, ResearchStatus $newStatus): void
    {
        $previousStatus = $research->status->value;

        $research->update([
            'status' => $newStatus,
        ]);

        app(ResearchCategoryService::class)->clearCache();

        if ($research->user) {
            $research->user->notify(new ResearchStatusChangedNotification($research, $previousStatus));
        }
    }

    /**
     * Increment the views count for a paper.
     */
    public function incrementViews(Research $research): void
    {
        $research->increment('views');
    }

    /**
     * Sync author relationships including the primary creator user.
     *
     * @param  array<int>  $coAuthorIds
     */
    protected function syncAuthors(Research $research, User $primaryUser, array $coAuthorIds): void
    {
        $authorPivotData = [];

        // Primary author always first
        $authorPivotData[$primaryUser->id] = ['author_order' => 1];

        $order = 2;
        foreach ($coAuthorIds as $coAuthorId) {
            if ($coAuthorId !== $primaryUser->id && ! isset($authorPivotData[$coAuthorId])) {
                $authorPivotData[$coAuthorId] = ['author_order' => $order++];
            }
        }

        $research->authors()->sync($authorPivotData);
    }

    /**
     * Process, resize, and convert uploaded thumbnail to WebP.
     */
    protected function processThumbnail(UploadedFile $file): string
    {
        // Try to process via Intervention Image using GD
        try {
            $manager = new ImageManager('gd');
            $image = $manager->decodePath($file->getRealPath());

            // Resize max 800x600 (scale down if larger) maintaining aspect ratio
            $image->scaleDown(width: 800, height: 600);

            // Convert to WebP and set quality to 82
            $encoded = $image->toWebp(82);
            $filename = Str::uuid().'.webp';

            Storage::disk('private_research')->put('thumbnails/'.$filename, (string) $encoded);

            return 'thumbnails/'.$filename;
        } catch (\Exception $e) {
            // Fallback if intervention fails (e.g., non-image, or bad driver)
            // Just store the original file directly
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();

            return $file->storeAs('thumbnails', $filename, 'private_research');
        }
    }
}
