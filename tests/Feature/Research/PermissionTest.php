<?php

namespace Tests\Feature\Research;

use App\Enums\DownloadPermission;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_download_permission_allows_anyone_to_download(): void
    {
        Storage::fake('private_research');

        $author = User::factory()->create();

        $pdf = UploadedFile::fake()->create('paper.pdf', 1000, 'application/pdf');
        $f = fopen($pdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);
        $pdfPath = $pdf->store('research_pdfs', 'private_research');

        $research = Research::create([
            'user_id' => $author->id,
            'title' => 'Open Access Paper',
            'slug' => 'open-access-paper',
            'abstract' => 'Open access abstract content for permission test.',
            'pdf_path' => $pdfPath,
            'download_permission' => DownloadPermission::FREE,
            'status' => 'published',
        ]);

        $response = $this->get("/research/{$research->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_restricted_download_permission_blocks_unauthorized_users(): void
    {
        Storage::fake('private_research');

        $author = User::factory()->create();
        $otherUser = User::factory()->create();

        $pdf = UploadedFile::fake()->create('paper.pdf', 1000, 'application/pdf');
        $f = fopen($pdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);
        $pdfPath = $pdf->store('research_pdfs', 'private_research');

        $research = Research::create([
            'user_id' => $author->id,
            'title' => 'Restricted Paper Title',
            'slug' => 'restricted-paper-title',
            'abstract' => 'Restricted paper abstract content.',
            'pdf_path' => $pdfPath,
            'download_permission' => DownloadPermission::RESTRICTED,
            'status' => 'published',
        ]);

        // Guest user blocked
        $this->get("/research/{$research->id}/download")->assertStatus(403);

        // Other logged in user blocked
        $this->actingAs($otherUser)->get("/research/{$research->id}/download")->assertStatus(403);

        // Author is allowed
        $this->actingAs($author)->get("/research/{$research->id}/download")->assertStatus(200);
    }
}
