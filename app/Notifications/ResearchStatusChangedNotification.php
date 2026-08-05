<?php

namespace App\Notifications;

use App\Models\Research;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->research->status->label();

        $mail = (new MailMessage)
            ->subject('Publication Status Update: '.$this->research->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('The publication status of your research paper "'.$this->research->title.'" has been updated to: '.$statusLabel.'.');

        if ($this->research->status->value === 'published') {
            $mail->action('View Publication Page', route('research.show', $this->research->slug));
        } else {
            $mail->action('Manage My Research', route('dashboard.research.index'));
        }

        return $mail->line('Thank you for contributing to PURE Research Hub!');
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
