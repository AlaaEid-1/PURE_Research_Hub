<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessResearchPdfJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessResearchPdfJobTest extends TestCase
{
    public function test_it_does_not_fail_if_pdf_is_missing(): void
    {
        Storage::fake('private_research');

        Log::shouldReceive('warning')
            ->once()
            ->with('ProcessResearchPdfJob: PDF file not found.', \Mockery::any());

        $job = new ProcessResearchPdfJob('missing.pdf');
        $job->handle();
    }

    public function test_it_logs_critical_failure_on_job_failed(): void
    {
        Log::shouldReceive('critical')
            ->once()
            ->with('ProcessResearchPdfJob failed completely after retries.', \Mockery::any());

        $job = new ProcessResearchPdfJob('test.pdf');
        $job->failed(new \Exception('Test failure'));
    }
}
