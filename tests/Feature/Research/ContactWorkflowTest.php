<?php

namespace Tests\Feature\Research;

use App\Enums\ContactRequestStatus;
use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use App\Events\ContactReplySent;
use App\Models\Research;
use App\Models\ResearchContactRequest;
use App\Models\User;
use App\Notifications\ContactRequestReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createResearch(User $owner, DownloadPermission $permission): Research
    {
        return Research::create([
            'user_id' => $owner->id,
            'title' => 'Contact Author Test Paper',
            'slug' => 'contact-test-'.uniqid(),
            'abstract' => 'Abstract for contact workflow test paper.',
            'pdf_path' => 'research_pdfs/contact_test.pdf',
            'download_permission' => $permission,
            'status' => ResearchStatus::PUBLISHED,
        ]);
    }

    public function test_free_research_allows_direct_download(): void
    {
        Storage::fake('private_research');
        Storage::disk('private_research')->put('research_pdfs/contact_test.pdf', 'PDF File Content');

        $owner = User::factory()->create();
        $user = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::FREE);

        $response = $this->actingAs($user)->get("/research/{$research->id}/download");
        $response->assertStatus(200);
    }

    public function test_contact_author_permission_denies_direct_pdf_download(): void
    {
        Storage::fake('private_research');

        $owner = User::factory()->create();
        $user = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $response = $this->actingAs($user)->get("/research/{$research->id}/download");
        $response->assertStatus(403);
    }

    public function test_contact_request_creates_correctly_and_notifies_author(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $sender = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $response = $this->actingAs($sender)->post("/research/{$research->id}/contact-request", [
            'subject' => 'Academic Inquiry regarding methodology',
            'message' => 'Hello Dr. Author, I am interested in discussing your findings.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('conversations', [
            'research_id' => $research->id,
            'sender_id' => $sender->id,
            'subject' => 'Academic Inquiry regarding methodology',
            'status' => \App\Enums\ConversationStatus::OPEN->value,
        ]);

        Notification::assertSentTo(
            $owner,
            \App\Notifications\NewInquiryReceivedNotification::class,
            function (\App\Notifications\NewInquiryReceivedNotification $notification) use ($research, $sender) {
                return $notification->conversation->research_id === $research->id
                    && $notification->conversation->sender_id === $sender->id;
            }
        );
    }

    public function test_author_and_sender_can_view_inquiry_thread(): void
    {
        $owner = User::factory()->create();
        $sender = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $contactRequest = ResearchContactRequest::create([
            'research_id' => $research->id,
            'sender_id' => $sender->id,
            'subject' => 'Inquiry Thread Access',
            'message' => 'Can we discuss your dataset?',
            'status' => ContactRequestStatus::PENDING,
        ]);

        // Sender can view
        $this->actingAs($sender)
            ->get("/dashboard/inquiries/{$contactRequest->id}")
            ->assertStatus(200)
            ->assertSee('Inquiry Thread Access');

        // Author can view
        $this->actingAs($owner)
            ->get("/dashboard/inquiries/{$contactRequest->id}")
            ->assertStatus(200)
            ->assertSee('Inquiry Thread Access');
    }

    public function test_unauthorized_users_cannot_view_inquiry_thread(): void
    {
        $owner = User::factory()->create();
        $sender = User::factory()->create();
        $stranger = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $contactRequest = ResearchContactRequest::create([
            'research_id' => $research->id,
            'sender_id' => $sender->id,
            'subject' => 'Private Inquiry',
            'message' => 'Private content message.',
            'status' => ContactRequestStatus::PENDING,
        ]);

        $this->actingAs($stranger)
            ->get("/dashboard/inquiries/{$contactRequest->id}")
            ->assertStatus(403);
    }

    public function test_author_can_reply_and_triggers_broadcasting(): void
    {
        Event::fake([ContactReplySent::class]);

        $owner = User::factory()->create();
        $sender = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $contactRequest = ResearchContactRequest::create([
            'research_id' => $research->id,
            'sender_id' => $sender->id,
            'subject' => 'Dataset Inquiry',
            'message' => 'Is your dataset publicly available?',
            'status' => ContactRequestStatus::PENDING,
        ]);

        $response = $this->actingAs($owner)->post("/dashboard/inquiries/{$contactRequest->id}/reply", [
            'message' => 'Yes, I can share the dataset link with you.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('research_contact_replies', [
            'contact_request_id' => $contactRequest->id,
            'user_id' => $owner->id,
            'message' => 'Yes, I can share the dataset link with you.',
        ]);

        $this->assertDatabaseHas('research_contact_requests', [
            'id' => $contactRequest->id,
            'status' => ContactRequestStatus::REPLIED->value,
        ]);

        Event::assertDispatched(ContactReplySent::class, function (ContactReplySent $event) use ($contactRequest, $owner) {
            return $event->reply->contact_request_id === $contactRequest->id
                && $event->reply->user_id === $owner->id;
        });
    }

    public function test_unauthorized_user_cannot_reply_to_inquiry(): void
    {
        $owner = User::factory()->create();
        $sender = User::factory()->create();
        $stranger = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $contactRequest = ResearchContactRequest::create([
            'research_id' => $research->id,
            'sender_id' => $sender->id,
            'subject' => 'Restricted Thread',
            'message' => 'Message contents.',
            'status' => ContactRequestStatus::PENDING,
        ]);

        $this->actingAs($stranger)
            ->post("/dashboard/inquiries/{$contactRequest->id}/reply", [
                'message' => 'Unauthorized intrusion reply.',
            ])
            ->assertStatus(403);
    }
}
