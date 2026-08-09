<?php

namespace App\Jobs;

use App\Models\Research;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessResearchThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120; // 2 minutes max

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Research $research,
        public string $rawPath
    ) {
        $this->onQueue('high');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('avatars');

        if (! $disk->exists($this->rawPath)) {
            Log::warning('ProcessResearchThumbnailJob: Raw image not found.', ['path' => $this->rawPath]);
            return;
        }

        try {
            $imageContent = $disk->get($this->rawPath);
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($imageContent);

            // Resize max 800x600 (scale down if larger) maintaining aspect ratio
            $image->scaleDown(width: 800, height: 600);

            try {
                // Attempt to convert to WebP
                $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(82));
                $extension = 'webp';
            } catch (\Exception $webpException) {
                Log::warning('WebP encoding failed in background job, falling back to JPEG', [
                    'research_id' => $this->research->id,
                    'exception' => $webpException->getMessage()
                ]);

                // Fallback to JPEG
                $encoded = $image->encode(new \Intervention\Image\Encoders\JpegEncoder(82));
                $extension = 'jpg';
            }

            $filename = Str::uuid().'.'.$extension;
            $newPath = 'thumbnails/'.$filename;

            $putResult = $disk->put($newPath, (string) $encoded);
            
            if (!$putResult) {
                throw new \Exception('Failed to upload optimized thumbnail to storage.');
            }

            // Update database without firing events
            $this->research->updateQuietly([
                'thumbnail_path' => $newPath
            ]);

            // Delete raw image
            $disk->delete($this->rawPath);

            Log::info('ProcessResearchThumbnailJob: Thumbnail successfully optimized.', [
                'research_id' => $this->research->id,
                'path' => $newPath
            ]);
            
        } catch (\Exception $e) {
            Log::error('ProcessResearchThumbnailJob: Failed to process image.', [
                'research_id' => $this->research->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('ProcessResearchThumbnailJob failed completely after retries.', [
            'research_id' => $this->research->id,
            'message' => $exception->getMessage(),
        ]);
    }
}
