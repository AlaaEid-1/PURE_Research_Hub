<?php

namespace Tests\Feature\Research;

use App\Enums\AccessRequestStatus;
use App\Enums\DownloadPermission;
use App\Models\Research;
use App\Models\ResearchAccessRequest;
use App\Models\User;
use App\Notifications\AccessRequestDecisionNotification;
use App\Notifications\AccessRequestReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccessRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_access_and_author_receives_notification(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $requester = User::factory()->create();

        $research = Research::create([
            'user_id' => $author->id,
            'title' => 'Quantum Cryptography Protocol',
            'slug' => 'quantum-cryptography-protocol',
            'abstract' => 'Sample abstract for quantum cryptography research paper.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'download_permission' => DownloadPermission::REQUEST_ACCESS,
            'status' => 'published',
        ]);

        $response = $this->actingAs($requester)->post("/research/{$research->id}/access-request", [
            'message' => 'I request access for my PhD research project in quantum protocols.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('research_access_requests', [
            'research_id' => $research->id,
            'requester_id' => $requester->id,
            'status' => AccessRequestStatus::PENDING->value,
        ]);

        Notification::assertSentTo($author, AccessRequestReceivedNotification::class);
    }

    public function test_author_can_approve_request_and_approved_user_downloads_pdf(): void
    {
        Notification::fake();
        Storage::fake('private_research');

        $author = User::factory()->create();
        $requester = User::factory()->create();

        $pdf = UploadedFile::fake()->create('paper.pdf', 1000, 'application/pdf');
        $f = fopen($pdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);
        $pdfPath = $pdf->store('research_pdfs', 'private_research');

        $research = Research::create([
            'user_id' => $author->id,
            'title' => 'Approved Quantum Paper',
            'slug' => 'approved-quantum-paper',
            'abstract' => 'Abstract for approved quantum paper.',
            'pdf_path' => $pdfPath,
            'download_permission' => DownloadPermission::REQUEST_ACCESS,
            'status' => 'published',
        ]);

        $accessRequest = ResearchAccessRequest::create([
            'research_id' => $research->id,
            'requester_id' => $requester->id,
            'message' => 'Valid PhD research request message text.',
            'status' => AccessRequestStatus::PENDING,
        ]);

        // Prior to approval, download is denied
        $this->actingAs($requester)->get("/research/{$research->id}/download")->assertStatus(403);

        // Author approves request
        $approveResponse = $this->actingAs($author)->post("/dashboard/requests/{$accessRequest->id}/approve");
        $approveResponse->assertRedirect();

        $accessRequest->refresh();
        $this->assertSame(AccessRequestStatus::APPROVED, $accessRequest->status);

        Notification::assertSentTo($requester, AccessRequestDecisionNotification::class);

        // After approval, requester can download PDF
        $downloadResponse = $this->actingAs($requester)->get("/research/{$research->id}/download");
        $downloadResponse->assertStatus(200);
        $downloadResponse->assertHeader('content-type', 'application/pdf');
    }
}
