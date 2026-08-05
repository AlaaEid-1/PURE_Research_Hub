<?php

namespace Tests\Feature\Admin;

use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\User;
use App\Notifications\ResearchStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminResearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_moderation_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($user)->get('/admin/research')->assertStatus(403);
    }

    public function test_admin_can_access_moderation_queue_and_approve_paper(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->create();

        $research = Research::create([
            'user_id' => $author->id,
            'title' => 'Pending Review Manuscript',
            'slug' => 'pending-review-manuscript',
            'abstract' => 'Sample abstract for manuscript under review.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'status' => ResearchStatus::PENDING,
        ]);

        $response = $this->actingAs($admin)->get('/admin/research?status=pending');
        $response->assertStatus(200);
        $response->assertSee('Pending Review Manuscript');

        Notification::fake();

        // Approve paper
        $approveResponse = $this->actingAs($admin)->post("/admin/research/{$research->id}/approve");
        $approveResponse->assertRedirect();

        $research->refresh();
        $this->assertSame(ResearchStatus::PUBLISHED, $research->status);

        Notification::assertSentTo(
            $author,
            ResearchStatusChangedNotification::class
        );
    }

    public function test_admin_can_reject_or_request_changes_on_paper(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $author = User::factory()->create();

        $research = Research::create([
            'user_id' => $author->id,
            'title' => 'Paper For Revision',
            'slug' => 'paper-for-revision',
            'abstract' => 'Abstract for revision testing.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'status' => ResearchStatus::PENDING,
        ]);

        // Request changes
        $this->actingAs($admin)->post("/admin/research/{$research->id}/request-changes")->assertRedirect();
        $research->refresh();
        $this->assertSame(ResearchStatus::UNDER_REVIEW, $research->status);

        Notification::assertSentTo(
            $author,
            ResearchStatusChangedNotification::class
        );

        // Reject paper
        $this->actingAs($admin)->post("/admin/research/{$research->id}/reject")->assertRedirect();
        $research->refresh();
        $this->assertSame(ResearchStatus::REJECTED, $research->status);
    }
}
