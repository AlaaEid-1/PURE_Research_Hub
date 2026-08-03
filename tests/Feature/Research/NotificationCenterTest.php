<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\ResearchAccessRequest;
use App\Models\User;
use App\Notifications\AccessRequestReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_notifications_and_mark_them_as_read(): void
    {
        $author = User::factory()->create();
        $requester = User::factory()->create();

        $research = Research::create([
            'user_id' => $author->id,
            'title' => 'Notification Target Paper',
            'slug' => 'notification-target-paper',
            'abstract' => 'Sample abstract for notification test.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'status' => 'published',
        ]);

        $accessRequest = ResearchAccessRequest::create([
            'research_id' => $research->id,
            'requester_id' => $requester->id,
            'message' => 'PhD research request message text.',
            'status' => 'pending',
        ]);

        $author->notify(new AccessRequestReceivedNotification($accessRequest));

        $this->assertSame(1, $author->unreadNotifications->count());

        $response = $this->actingAs($author)->get('/dashboard/notifications');
        $response->assertStatus(200);
        $response->assertSee('Notification Target Paper');

        $notificationId = $author->unreadNotifications->first()->id;

        // Mark single as read
        $markReadResponse = $this->actingAs($author)->post("/dashboard/notifications/{$notificationId}/read");
        $markReadResponse->assertRedirect();

        $author->refresh();
        $this->assertSame(0, $author->unreadNotifications->count());
    }
}
