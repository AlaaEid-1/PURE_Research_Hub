<?php

namespace App\Notifications;

use App\Models\Research;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ResearchStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Research $research,
        public string $previousStatus
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'research_id' => $this->research->id,
            'research_title' => $this->research->title,
            'status' => $this->research->status->value,
            'previous_status' => $this->previousStatus,
        ];
    }
}
