<?php

namespace Tests\Feature\Research;

use App\Enums\ConversationStatus;
use App\Enums\DownloadPermission;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Research;
use App\Models\ResearchAccessGrant;
use App\Models\User;
use App\Notifications\NewInquiryReceivedNotification;
use App\Notifications\NewMessageReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ResearcherCommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected function createResearch(User $owner, DownloadPermission $permission): Research
    {
        return Research::create([
            'user_id' => $owner->id,
            'title' => 'Researcher Communication Test Paper',
            'slug' => 'comm-test-'.uniqid(),
            'abstract' => 'Abstract for researcher communication test paper.',
            'pdf_path' => 'research_pdfs/comm_test.pdf',
            'download_permission' => $permission,
            'status' => \App\Enums\ResearchStatus::PUBLISHED,
        ]);
    }

    public function test_free_permission_allows_direct_download(): void
    {
        Storage::fake('private_research');
        Storage::disk('private_research')->put('research_pdfs/comm_test.pdf', 'PDF CONTENT');

        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::FREE);

        $response = $this->actingAs($visitor)->get("/research/{$research->id}/download");
        $response->assertOk();
    }

    public function test_contact_author_permission_blocks_direct_download_until_access_granted(): void
    {
        Storage::fake('private_research');
        Storage::disk('private_research')->put('research_pdfs/comm_test.pdf', 'PDF CONTENT');

        $owner = User::factory()->create();
        $inquirer = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        // Direct download is blocked with 403 Forbidden
        $response = $this->actingAs($inquirer)->get("/research/{$research->id}/download");
        $response->assertStatus(403);

        // Grant access
        ResearchAccessGrant::create([
            'research_id' => $research->id,
            'user_id' => $inquirer->id,
            'approved_by' => $owner->id,
            'approved_at' => now(),
        ]);

        // Direct download now succeeds
        $response = $this->actingAs($inquirer)->get("/research/{$research->id}/download");
        $response->assertOk();
    }

    public function test_contact_author_creates_conversation_and_notifies_author(): void
    {
        Notification::fake();

        $owner = User::factory()->create();
        $inquirer = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $response = $this->actingAs($inquirer)->post("/research/{$research->id}/contact-request", [
            'subject' => 'Methodology Inquiry',
            'message' => 'Hello Dr. Author, I would like to discuss your research methodology.',
        ]);

        $conversation = Conversation::first();
        $this->assertNotNull($conversation);
        $this->assertEquals($research->id, $conversation->research_id);
        $this->assertEquals($inquirer->id, $conversation->sender_id);
        $this->assertEquals('Methodology Inquiry', $conversation->subject);

        $response->assertRedirect(route('dashboard.conversations.show', $conversation));

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $inquirer->id,
            'body' => 'Hello Dr. Author, I would like to discuss your research methodology.',
        ]);

        Notification::assertSentTo(
            $owner,
            NewInquiryReceivedNotification::class,
            fn ($notification) => $notification->conversation->id === $conversation->id
        );
    }

    public function test_participants_can_send_messages_and_trigger_realtime_broadcast_event(): void
    {
        Event::fake([MessageSent::class]);
        Notification::fake();

        $owner = User::factory()->create();
        $inquirer = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $conversation = Conversation::create([
            'research_id' => $research->id,
            'author_id' => $owner->id,
            'sender_id' => $inquirer->id,
            'subject' => 'Methodology Inquiry',
            'status' => ConversationStatus::OPEN,
            'last_message_at' => now(),
        ]);

        // Author posts a reply message
        $response = $this->actingAs($owner)->post("/dashboard/conversations/{$conversation->id}/messages", [
            'body' => 'Thank you for reaching out! What specific questions do you have?',
        ]);

        $response->assertRedirect();

        $message = Message::latest()->first();
        $this->assertEquals('Thank you for reaching out! What specific questions do you have?', $message->body);

        Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($message) {
            return $event->message->id === $message->id;
        });

        Notification::assertSentTo($inquirer, NewMessageReceivedNotification::class);
    }

    public function test_author_can_grant_access_from_conversation_thread(): void
    {
        $owner = User::factory()->create();
        $inquirer = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $conversation = Conversation::create([
            'research_id' => $research->id,
            'author_id' => $owner->id,
            'sender_id' => $inquirer->id,
            'subject' => 'Permission Request',
            'status' => ConversationStatus::OPEN,
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($owner)->post("/dashboard/conversations/{$conversation->id}/grant-access");
        $response->assertRedirect();

        $this->assertDatabaseHas('research_access_grants', [
            'research_id' => $research->id,
            'user_id' => $inquirer->id,
            'approved_by' => $owner->id,
        ]);
    }

    public function test_unauthorized_users_cannot_access_or_reply_to_conversations(): void
    {
        $owner = User::factory()->create();
        $inquirer = User::factory()->create();
        $outsider = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $conversation = Conversation::create([
            'research_id' => $research->id,
            'author_id' => $owner->id,
            'sender_id' => $inquirer->id,
            'subject' => 'Private Inquiry',
            'status' => ConversationStatus::OPEN,
            'last_message_at' => now(),
        ]);

        // Outsider view attempt -> 403
        $response = $this->actingAs($outsider)->get("/dashboard/conversations/{$conversation->id}");
        $response->assertStatus(403);

        // Outsider reply attempt -> 403
        $response = $this->actingAs($outsider)->post("/dashboard/conversations/{$conversation->id}/messages", [
            'body' => 'I am intruding in this conversation',
        ]);
        $response->assertStatus(403);
    }

    public function test_guest_inquiry_generates_signed_route(): void
    {
        $owner = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $response = $this->post("/research/{$research->id}/contact-request", [
            'guest_name' => 'Jane Guest',
            'guest_email' => 'jane@example.com',
            'subject' => 'Guest Question',
            'message' => 'I am a guest interested in your publication.',
        ]);

        $conversation = Conversation::where('guest_email', 'jane@example.com')->first();
        $this->assertNotNull($conversation);
        $this->assertNull($conversation->sender_id);

        $signedUrl = URL::signedRoute('guest.conversations.show', ['conversation' => $conversation->id]);

        $response = $this->get($signedUrl);
        $response->assertOk();
    }

    public function test_notification_broadcast_payload_structure_and_channel_security(): void
    {
        $owner = User::factory()->create();
        $inquirer = User::factory()->create();
        $research = $this->createResearch($owner, DownloadPermission::CONTACT_AUTHOR);

        $conversation = Conversation::create([
            'research_id' => $research->id,
            'author_id' => $owner->id,
            'sender_id' => $inquirer->id,
            'subject' => 'Methodology Question',
            'status' => ConversationStatus::OPEN,
            'last_message_at' => now(),
        ]);

        $notification = new NewInquiryReceivedNotification($conversation);
        $broadcastData = $notification->toBroadcast($owner)->data;

        $this->assertArrayHasKey('id', $broadcastData);
        $this->assertArrayHasKey('type', $broadcastData);
        $this->assertArrayHasKey('title', $broadcastData);
        $this->assertArrayHasKey('message', $broadcastData);
        $this->assertArrayHasKey('action_url', $broadcastData);
        $this->assertArrayHasKey('created_at', $broadcastData);
    }
}
