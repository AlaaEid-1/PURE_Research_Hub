<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessResearchPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $pdfPath
    ) {
        $this->onQueue('high');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('private_research');

        if (! $disk->exists($this->pdfPath)) {
            Log::warning('ProcessResearchPdfJob: PDF file not found.', ['path' => $this->pdfPath]);

            return;
        }

        $pdfContent = $disk->get($this->pdfPath);
        $tempInputPath = storage_path('app/private/tmp_' . Str::random(16) . '.pdf');
        $tempOutputPath = $tempInputPath . '.sanitized.tmp';

        file_put_contents($tempInputPath, $pdfContent);

        // Using Symfony Process for secure command execution
        // exiftool -all:all= -o output_file input_file
        $process = new Process([
            'exiftool',
            '-all:all=',
            '-o',
            $tempOutputPath,
            $tempInputPath,
        ]);

        $process->setTimeout(240); // 4 minutes max for exiftool

        try {
            if (! app()->runningUnitTests()) {
                $process->mustRun();
            } else {
                // Mock behavior for testing
                copy($tempInputPath, $tempOutputPath);
            }
        } catch (ProcessFailedException $exception) {
            Log::error('ProcessResearchPdfJob: exiftool failed.', [
                'path' => $this->pdfPath,
                'error' => $exception->getMessage(),
            ]);

            // Ensure cleanup of partial tmp file if it exists
            if (file_exists($tempInputPath)) @unlink($tempInputPath);
            if (file_exists($tempOutputPath)) @unlink($tempOutputPath);

            throw $exception;
        }

        // Verify the output file was created and is a valid file
        if (file_exists($tempOutputPath) && filesize($tempOutputPath) > 0) {
            // Replace the original file with the sanitized copy
            $stream = fopen($tempOutputPath, 'r');
            $disk->put($this->pdfPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            Log::info('ProcessResearchPdfJob: PDF successfully sanitized.', ['path' => $this->pdfPath]);
        } else {
            Log::error('ProcessResearchPdfJob: Sanitized output file is invalid or empty.', ['path' => $tempOutputPath]);
            if (file_exists($tempInputPath)) @unlink($tempInputPath);
            if (file_exists($tempOutputPath)) @unlink($tempOutputPath);
            throw new \Exception('Exiftool did not produce a valid output file.');
        }
        
        // Cleanup temp files
        if (file_exists($tempInputPath)) @unlink($tempInputPath);
        if (file_exists($tempOutputPath)) @unlink($tempOutputPath);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('ProcessResearchPdfJob failed completely after retries.', [
            'pdf_path' => $this->pdfPath,
            'message' => $exception->getMessage(),
        ]);
    }
}
