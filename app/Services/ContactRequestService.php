<?php

namespace App\Services;

use App\Enums\ContactRequestStatus;
use App\Events\ContactReplySent;
use App\Models\Research;
use App\Models\ResearchContactReply;
use App\Models\ResearchContactRequest;
use App\Models\User;
use App\Notifications\ContactRequestReceivedNotification;

class ContactRequestService
{
    /**
     * Create a new contact inquiry message for a research paper author and notify the author.
     */
    public function createInquiry(User $sender, Research $research, string $message, ?string $subject = null): ResearchContactRequest
    {
        $subject = $subject ?: 'Inquiry regarding: '.$research->title;

        $contactRequest = ResearchContactRequest::create([
            'research_id' => $research->id,
            'sender_id' => $sender->id,
            'subject' => $subject,
            'message' => $message,
            'status' => ContactRequestStatus::PENDING,
        ]);

        $contactRequest->load(['research', 'sender']);

        // Notify paper author
        if ($research->user && $research->user_id !== $sender->id) {
            $research->user->notify(new ContactRequestReceivedNotification($contactRequest));
        }

        return $contactRequest;
    }

    /**
     * Reply to an existing contact inquiry conversation.
     */
    public function replyToInquiry(ResearchContactRequest $contactRequest, User $user, string $message): ResearchContactReply
    {
        $reply = ResearchContactReply::create([
            'contact_request_id' => $contactRequest->id,
            'user_id' => $user->id,
            'message' => $message,
        ]);

        // Update inquiry status to replied
        $contactRequest->update([
            'status' => ContactRequestStatus::REPLIED,
        ]);

        $reply->load(['user', 'contactRequest']);

        // Broadcast realtime reply event
        event(new ContactReplySent($reply));

        return $reply;
    }

    /**
     * Mark a contact inquiry conversation as closed.
     */
    public function closeInquiry(ResearchContactRequest $contactRequest): void
    {
        $contactRequest->update([
            'status' => ContactRequestStatus::CLOSED,
        ]);
    }
}
