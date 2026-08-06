<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessGrantedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Conversation $conversation
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $downloadUrl = route('research.download', $this->conversation->research);

        return (new MailMessage)
            ->subject('Download Access Granted: '.$this->conversation->research?->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Great news! The author of "'.$this->conversation->research?->title.'" has granted you PDF download access.')
            ->action('Download PDF Paper', $downloadUrl)
            ->line('You can also view your full conversation history in your dashboard.')
            ->line('Thank you for using PURE Research Hub!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'research_id' => $this->conversation->research_id,
            'research_title' => $this->conversation->research?->title,
            'message' => 'The author has granted you PDF download access for this publication.',
            'link' => route('research.download', $this->conversation->research),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'id' => $this->id,
            'type' => static::class,
            'title' => 'Download Access Granted',
            'message' => 'The author has granted you PDF download access for "'.$this->conversation->research?->title.'"',
            'action_url' => route('research.download', $this->conversation->research),
            'created_at' => now()->toIso8601String(),
            'formatted_time' => now()->diffForHumans(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('AccessGrantedNotification failed', [
            'conversation_id' => $this->conversation->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
