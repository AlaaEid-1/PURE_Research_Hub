<?php

namespace Tests\Feature\Research;

use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DraftWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_researcher_can_save_paper_as_draft(): void
    {
        Storage::fake('private_research');

        $user = User::factory()->create();

        $pdfFile = UploadedFile::fake()->create('paper.pdf', 1000, 'application/pdf');
        $f = fopen($pdfFile->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);

        $response = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Unfinished Draft Paper Title',
            'abstract' => 'Sample draft abstract content for testing.',
            'download_permission' => 'free',
            'pdf_file' => $pdfFile,
            'submit_action' => 'draft',
        ]);

        $response->assertRedirect();

        $research = Research::where('title', 'Unfinished Draft Paper Title')->firstOrFail();
        $this->assertSame(ResearchStatus::DRAFT, $research->status);

        // Draft papers are hidden from public catalog
        $catalogResponse = $this->get('/research');
        $catalogResponse->assertDontSee('Unfinished Draft Paper Title');
    }

    public function test_researcher_can_update_draft_and_submit_for_moderation(): void
    {
        Storage::fake('private_research');

        $user = User::factory()->create();

        $research = Research::create([
            'user_id' => $user->id,
            'title' => 'Initial Draft Manuscript',
            'slug' => 'initial-draft-manuscript',
            'abstract' => 'Initial abstract content.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'status' => ResearchStatus::DRAFT,
        ]);

        $response = $this->actingAs($user)->put("/dashboard/research/{$research->id}", [
            'title' => 'Finalized Manuscript Ready For Review',
            'abstract' => 'Updated abstract content with completed research results.',
            'download_permission' => 'free',
            'submit_action' => 'submit',
        ]);

        $response->assertRedirect();

        $research->refresh();
        $this->assertSame(ResearchStatus::PUBLISHED, $research->status);
    }
}
