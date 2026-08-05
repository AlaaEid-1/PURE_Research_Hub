<?php

namespace App\Services;

use App\Enums\ConversationStatus;
use App\Enums\ConversationType;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Research;
use App\Models\ResearchAccessGrant;
use App\Models\User;
use App\Notifications\NewInquiryReceivedNotification;
use App\Notifications\NewMessageReceivedNotification;

class ConversationService
{
    /**
     * Start a new conversation for a research publication.
     */
    public function startConversation(
        Research $research,
        ?User $sender,
        string $messageBody,
        ?string $subject = null,
        ?string $guestName = null,
        ?string $guestEmail = null,
        ConversationType $type = ConversationType::GENERAL_INQUIRY
    ): Conversation {
        $subject = $subject ?: 'Inquiry regarding: '.$research->title;

        $conversation = Conversation::create([
            'research_id' => $research->id,
            'author_id' => $research->user_id,
            'sender_id' => $sender?->id,
            'guest_name' => $sender ? null : $guestName,
            'guest_email' => $sender ? null : $guestEmail,
            'type' => $type,
            'subject' => $subject,
            'status' => ConversationStatus::OPEN,
            'last_message_at' => now(),
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender?->id,
            'body' => $messageBody,
        ]);

        $conversation->load(['research', 'author', 'sender', 'latestMessage']);

        // Notify research paper author
        if ($research->user && ($sender === null || $research->user_id !== $sender->id)) {
            $research->user->notify(new NewInquiryReceivedNotification($conversation));
        }

        return $conversation;
    }

    /**
     * Send a reply message in an existing conversation thread.
     */
    public function sendMessage(Conversation $conversation, ?User $sender, string $body): Message
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender?->id,
            'body' => $body,
        ]);

        $conversation->update([
            'status' => ConversationStatus::REPLIED,
            'last_message_at' => now(),
        ]);

        $message->load(['conversation', 'sender']);

        // Dispatch real-time chat broadcast event
        event(new MessageSent($message));

        // Determine notification recipient (author or sender)
        if ($sender) {
            $recipient = $sender->id === $conversation->author_id 
                ? $conversation->sender 
                : $conversation->author;

            if ($recipient) {
                $recipient->notify(new NewMessageReceivedNotification($conversation, $message));
            }
        }

        return $message;
    }

    /**
     * Grant PDF download access to the inquirer.
     */
    public function grantAccess(Conversation $conversation, User $approvedBy, ?int $expiresInDays = null): ResearchAccessGrant
    {
        $recipientId = $conversation->sender_id;

        if (! $recipientId) {
            throw new \InvalidArgumentException('Download access can only be granted to registered user accounts.');
        }

        $expiresAt = $expiresInDays ? now()->addDays($expiresInDays) : null;

        $grant = ResearchAccessGrant::updateOrCreate(
            [
                'research_id' => $conversation->research_id,
                'user_id' => $recipientId,
            ],
            [
                'approved_by' => $approvedBy->id,
                'approved_at' => now(),
                'expires_at' => $expiresAt,
            ]
        );

        // Send a automated message in conversation thread informing user of granted access
        $this->sendMessage(
            $conversation,
            $approvedBy,
            'Download access has been granted for this research paper.'
        );

        // Notify inquirer via database and email
        if ($conversation->sender) {
            $conversation->sender->notify(new \App\Notifications\AccessGrantedNotification($conversation));
        }

        return $grant;
    }

    /**
     * Mark unread messages in conversation as read for given user.
     */
    public function markAsRead(Conversation $conversation, User $user): void
    {
        Message::where('conversation_id', $conversation->id)
            ->where(function ($query) use ($user) {
                $query->whereNull('sender_id')
                    ->orWhere('sender_id', '!=', $user->id);
            })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Close a conversation thread.
     */
    public function closeConversation(Conversation $conversation): void
    {
        $conversation->update([
            'status' => ConversationStatus::CLOSED,
        ]);
    }
}
