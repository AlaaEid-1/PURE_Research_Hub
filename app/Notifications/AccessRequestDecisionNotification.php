<?php

namespace App\Notifications;

use App\Models\ResearchAccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestDecisionNotification extends Notification
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
        $statusLabel = $this->accessRequest->status->label();
        $isApproved = $this->accessRequest->status->value === 'approved';

        $mail = (new MailMessage)
            ->subject('PDF Access Request Update: '.$this->accessRequest->research->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your PDF access request for "'.$this->accessRequest->research->title.'" has been updated: '.$statusLabel.'.');

        if ($isApproved) {
            $mail->action('Download PDF Now', route('research.show', $this->accessRequest->research->slug));
        }

        return $mail->line('Thank you for using PURE Research Hub!');
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
            'status' => $this->accessRequest->status->value,
        ];
    }
}
