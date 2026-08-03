<?php

namespace App\Notifications;

use App\Models\ResearchAccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ResearchAccessRequest $accessRequest
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New PDF Access Request: '.$this->accessRequest->research->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($this->accessRequest->requester->name.' has requested PDF access to your research paper.')
            ->line('Paper: "'.$this->accessRequest->research->title.'"')
            ->line('Message from requester: "'.$this->accessRequest->message.'"')
            ->action('Review Request', route('dashboard.requests.index'))
            ->line('Thank you for contributing to PURE Research Hub!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'access_request_id' => $this->accessRequest->id,
            'research_id' => $this->accessRequest->research_id,
            'research_title' => $this->accessRequest->research->title,
            'requester_name' => $this->accessRequest->requester->name,
            'message' => $this->accessRequest->message,
        ];
    }
}
