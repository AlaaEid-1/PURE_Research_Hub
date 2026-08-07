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
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class ResearchService
{
    /**
     * Create a new research publication record and handle PDF upload.
     */
    public function createResearch(User $user, ResearchData $dto): Research
    {
        $startTime = microtime(true);

        $pdfPath = null;
        $thumbnailPath = null;

        try {
            if ($dto->pdfFile) {
                // $magic = file_get_contents($dto->pdfFile->getRealPath(), false, null, 0, 4);
                // if ($magic !== '%PDF') {
                //     throw ValidationException::withMessages(['pdf_file' => 'The uploaded file is not a valid PDF document.']);
                // }

                $safeFilename = Str::uuid().'.pdf';
                $tPdfStart = microtime(true);
                Log::info('PERF_UPLOAD: PDF_STORAGE_STARTED timestamp=' . $tPdfStart);
                $pdfPath = $dto->pdfFile->storeAs('research_pdfs', $safeFilename, 'private_research');
                Log::info('PERF_UPLOAD: PDF_STORAGE_FINISHED timestamp=' . microtime(true) . ' elapsed=' . round((microtime(true) - $tPdfStart) * 1000) . 'ms');
            }

            if ($dto->thumbnailFile) {
                $rawFilename = 'raw_'.Str::uuid().'.'.$dto->thumbnailFile->getClientOriginalExtension();
                $tThumbStart = microtime(true);
                Log::info('PERF_UPLOAD: THUMBNAIL_STORAGE_STARTED timestamp=' . $tThumbStart);
                $thumbnailPath = $dto->thumbnailFile->storeAs('thumbnails', $rawFilename, 'public');
                Log::info('PERF_UPLOAD: THUMBNAIL_STORAGE_FINISHED timestamp=' . microtime(true) . ' elapsed=' . round((microtime(true) - $tThumbStart) * 1000) . 'ms');
            }

            $tDbStart = microtime(true);
            Log::info('PERF_UPLOAD: DATABASE_TRANSACTION_STARTED timestamp=' . $tDbStart);
            $research = \Illuminate\Support\Facades\DB::transaction(function () use ($user, $dto, $pdfPath, $thumbnailPath) {
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
                
                return $research;
            });
            Log::info('PERF_UPLOAD: DATABASE_TRANSACTION_FINISHED timestamp=' . microtime(true) . ' elapsed=' . round((microtime(true) - $tDbStart) * 1000) . 'ms');

            $tJobStart = microtime(true);
            Log::info('PERF_UPLOAD: JOB_DISPATCH_STARTED timestamp=' . $tJobStart);
            if ($pdfPath) {
                ProcessResearchPdfJob::dispatch($pdfPath);
            }
            if ($thumbnailPath) {
                \App\Jobs\ProcessResearchThumbnailJob::dispatch($research, $thumbnailPath);
            }
            Log::info('PERF_UPLOAD: JOB_DISPATCH_FINISHED timestamp=' . microtime(true) . ' elapsed=' . round((microtime(true) - $tJobStart) * 1000) . 'ms');

            return $research;

        } catch (\Exception $e) {
            if ($pdfPath) {
                Storage::disk('private_research')->delete($pdfPath);
            }
            if ($thumbnailPath) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            \Illuminate\Support\Facades\Log::error('Research creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'filename' => $dto->pdfFile?->getClientOriginalName(),
                'size' => $dto->pdfFile?->getSize(),
                'mime' => $dto->pdfFile?->getMimeType(),
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Update an existing research publication.
     */
    public function updateResearch(Research $research, ResearchData $dto): Research
    {
        $newPdfPath = null;
        $newThumbnailPath = null;
        $newPdfUploaded = false;

        try {
            if ($dto->pdfFile) {
                // $magic = file_get_contents($dto->pdfFile->getRealPath(), false, null, 0, 4);
                // if ($magic !== '%PDF') {
                //     throw ValidationException::withMessages(['pdf_file' => 'The uploaded replacement file is not a valid PDF document.']);
                // }

                $safeFilename = Str::uuid().'.pdf';
                $newPdfPath = $dto->pdfFile->storeAs('research_pdfs', $safeFilename, 'private_research');
                $newPdfUploaded = true;
            }

            if ($dto->thumbnailFile) {
                $rawFilename = 'raw_'.Str::uuid().'.'.$dto->thumbnailFile->getClientOriginalExtension();
                $newThumbnailPath = $dto->thumbnailFile->storeAs('thumbnails', $rawFilename, 'public');
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($research, $dto, $newPdfPath, $newThumbnailPath) {
                $research->update([
                    'category_id' => $dto->categoryId,
                    'title' => $dto->title,
                    'abstract' => $dto->abstract,
                    'keywords' => $dto->keywords,
                    'doi' => $dto->doi,
                    'publication_date' => $dto->publicationDate,
                    'pdf_path' => $newPdfPath ?? $research->pdf_path,
                    'thumbnail_path' => $newThumbnailPath ?? $research->thumbnail_path,
                    'copyright_information' => $dto->copyrightInformation,
                    'download_permission' => $dto->downloadPermission,
                    'status' => $dto->status,
                ]);

                $this->syncAuthors($research, $research->user, $dto->coAuthorIds);
            });

            if ($newPdfUploaded && $newPdfPath) {
                ProcessResearchPdfJob::dispatch($newPdfPath);
            }
            if ($newThumbnailPath) {
                \App\Jobs\ProcessResearchThumbnailJob::dispatch($research, $newThumbnailPath);
            }

            return $research;

        } catch (\Exception $e) {
            if ($newPdfPath) {
                Storage::disk('private_research')->delete($newPdfPath);
            }
            if ($newThumbnailPath) {
                Storage::disk('public')->delete($newThumbnailPath);
            }

            \Illuminate\Support\Facades\Log::error('Research update failed: ' . $e->getMessage(), [
                'user_id' => $research->user_id,
                'research_id' => $research->id,
                'filename' => $dto->pdfFile?->getClientOriginalName(),
                'size' => $dto->pdfFile?->getSize(),
                'exception' => $e
            ]);

            throw $e;
        }
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

}
