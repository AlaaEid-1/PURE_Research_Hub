<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_download_creates_research_download_log(): void
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
            'title' => 'Analytics Logged Paper',
            'slug' => 'analytics-logged-paper',
            'abstract' => 'Abstract for download log testing.',
            'pdf_path' => $pdfPath,
            'download_permission' => 'free',
            'downloads' => 0,
        ]);

        $response = $this->actingAs($user)->get("/research/{$research->id}/download");

        $response->assertStatus(200);

        $this->assertDatabaseHas('research_download_logs', [
            'research_id' => $research->id,
            'user_id' => $user->id,
        ]);
    }
}
