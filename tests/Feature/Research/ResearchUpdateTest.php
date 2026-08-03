<?php

namespace Tests\Feature\Research;

use App\Enums\DownloadPermission;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResearchUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_paper_metadata_and_replace_pdf(): void
    {
        Storage::fake('private_research');

        $user = User::factory()->create();

        $oldPdf = UploadedFile::fake()->create('old.pdf', 1000, 'application/pdf');
        $f = fopen($oldPdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);
        $oldPath = $oldPdf->store('research_pdfs', 'private_research');

        $research = Research::create([
            'user_id' => $user->id,
            'title' => 'Original Paper Title',
            'slug' => 'original-paper-title',
            'abstract' => 'Original research abstract summary content.',
            'pdf_path' => $oldPath,
            'download_permission' => DownloadPermission::FREE,
        ]);

        $newPdf = UploadedFile::fake()->create('new.pdf', 1000, 'application/pdf');
        $f = fopen($newPdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);

        $response = $this->actingAs($user)->put("/dashboard/research/{$research->id}", [
            'title' => 'Revised Paper Title',
            'abstract' => 'Updated research abstract summary content with revisions.',
            'download_permission' => DownloadPermission::CONTACT_AUTHOR->value,
            'pdf_file' => $newPdf,
        ]);

        $response->assertRedirect('/dashboard/research');
        $response->assertSessionHas('success');

        $research->refresh();

        $this->assertSame('Revised Paper Title', $research->title);
        $this->assertSame('revised-paper-title', $research->slug);
        $this->assertSame(DownloadPermission::CONTACT_AUTHOR, $research->download_permission);

        // Verify old PDF was cleaned up and new PDF stored
        Storage::disk('private_research')->assertMissing($oldPath);
        Storage::disk('private_research')->assertExists($research->pdf_path);
    }
}
