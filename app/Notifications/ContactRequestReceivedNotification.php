<?php

namespace App\Notifications;

use App\Models\ResearchContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ContactRequestReceivedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public ResearchContactRequest $contactRequest
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'contact_request_id' => $this->contactRequest->id,
            'research_id' => $this->contactRequest->research_id,
            'research_title' => $this->contactRequest->research->title,
            'sender_name' => $this->contactRequest->sender->name,
            'message' => $this->contactRequest->message,
            'link' => route('dashboard.inquiries.show', $this->contactRequest),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contact_request_id' => $this->contactRequest->id,
            'research_id' => $this->contactRequest->research_id,
            'research_title' => $this->contactRequest->research->title,
            'sender_name' => $this->contactRequest->sender->name,
            'sender_email' => $this->contactRequest->sender->email,
            'message' => $this->contactRequest->message,
            'link' => route('dashboard.inquiries.show', $this->contactRequest),
        ];
    }
}
