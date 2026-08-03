<?php

namespace Tests\Feature\Research;

use App\Enums\AccessRequestStatus;
use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\ResearchAccessRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadPermissionTest extends TestCase
{
    use RefreshDatabase;

    private function createResearch(User $owner, DownloadPermission $permission, array $overrides = []): Research
    {
        return Research::create(array_merge([
            'user_id' => $owner->id,
            'title' => 'Download Permission Test Paper',
            'slug' => 'download-test-'.uniqid(),
            'abstract' => 'Abstract content for download permission testing.',
            'pdf_path' => 'research_pdfs/test.pdf',
            'download_permission' => $permission,
            'status' => ResearchStatus::PUBLISHED,
        ], $overrides));
    }

    public function test_free_research_can_be_downloaded_by_guests(): void
    {
        Storage::fake('private_research');
        Storage::disk('private_research')->put('research_pdfs/test.pdf', 'PDF content here');

        $owner = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::FREE);

        // Unauthenticated guest access
        $response = $this->get("/research/{$research->id}/download");

        // Should stream the file (200) not redirect to login
        $response->assertStatus(200);
    }

    public function test_restricted_research_cannot_be_downloaded_by_anyone(): void
    {
        Storage::fake('private_research');

        $owner = User::factory()->create();
        $user = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::RESTRICTED);

        $response = $this->actingAs($user)->get("/research/{$research->id}/download");

        $response->assertStatus(403);
    }

    public function test_owner_can_download_their_restricted_research(): void
    {
        Storage::fake('private_research');
        Storage::disk('private_research')->put('research_pdfs/test.pdf', 'PDF content here');

        $owner = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::RESTRICTED);

        $response = $this->actingAs($owner)->get("/research/{$research->id}/download");

        $response->assertStatus(200);
    }

    public function test_request_access_research_denied_without_approved_request(): void
    {
        Storage::fake('private_research');

        $owner = User::factory()->create();
        $user = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::REQUEST_ACCESS);

        $response = $this->actingAs($user)->get("/research/{$research->id}/download");

        $response->assertStatus(403);
    }

    public function test_request_access_research_allowed_with_approved_request(): void
    {
        Storage::fake('private_research');
        Storage::disk('private_research')->put('research_pdfs/test.pdf', 'PDF content here');

        $owner = User::factory()->create();
        $user = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::REQUEST_ACCESS);

        // Create an approved access request for this user
        ResearchAccessRequest::create([
            'research_id' => $research->id,
            'requester_id' => $user->id,
            'message' => 'Please grant access.',
            'status' => AccessRequestStatus::APPROVED,
        ]);

        $response = $this->actingAs($user)->get("/research/{$research->id}/download");

        $response->assertStatus(200);
    }

    public function test_contact_author_research_cannot_be_downloaded_directly(): void
    {
        Storage::fake('private_research');

        $owner = User::factory()->create();
        $user = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $response = $this->actingAs($user)->get("/research/{$research->id}/download");

        $response->assertStatus(403);
    }
}
