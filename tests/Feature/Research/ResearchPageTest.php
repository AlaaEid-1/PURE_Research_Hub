<?php

namespace Tests\Feature\Research;

use App\Enums\DownloadPermission;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResearchPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_research_catalog_loads(): void
    {
        $response = $this->get('/research');

        $response->assertStatus(200);
        $response->assertSee('Research Publication Catalog');
    }

    public function test_public_research_detail_page_loads_and_increments_views(): void
    {
        $user = User::factory()->create();

        $research = Research::create([
            'user_id' => $user->id,
            'title' => 'Genomics Analysis Breakthrough',
            'slug' => 'genomics-analysis-breakthrough',
            'abstract' => 'Comprehensive abstract detailing genomics breakthrough findings.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'download_permission' => DownloadPermission::FREE,
            'views' => 0,
        ]);

        $response = $this->get('/research/genomics-analysis-breakthrough');

        $response->assertStatus(200);
        $response->assertSee('Genomics Analysis Breakthrough');

        $research->refresh();
        $this->assertSame(1, $research->views);
    }

    public function test_free_download_streams_pdf_and_increments_download_counter(): void
    {
        Storage::fake('private_research');

        $user = User::factory()->create();

        $pdf = UploadedFile::fake()->create('paper.pdf', 1000, 'application/pdf');
        $f = fopen($pdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);
        $pdfPath = $pdf->store('research_pdfs', 'private_research');

        $research = Research::create([
            'user_id' => $user->id,
            'title' => 'Genomics Access Document',
            'slug' => 'genomics-access-document',
            'abstract' => 'Abstract for download test document.',
            'pdf_path' => $pdfPath,
            'download_permission' => DownloadPermission::FREE,
            'downloads' => 0,
        ]);

        $response = $this->get("/research/{$research->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $research->refresh();
        $this->assertSame(1, $research->downloads);
    }
}
