<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiryReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Conversation $conversation
    ) {}

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
        $senderName = $this->conversation->sender ? $this->conversation->sender->name : $this->conversation->guest_name;
        $url = route('dashboard.conversations.show', $this->conversation);

        return (new MailMessage)
            ->subject('New Inquiry Received: '.$this->conversation->subject)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($senderName.' sent an inquiry regarding your paper "'.$this->conversation->research->title.'".')
            ->line('Subject: "'.$this->conversation->subject.'"')
            ->line('Message: "'.$this->conversation->latestMessage?->body.'"')
            ->action('View Conversation Thread', $url)
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
            'research_title' => $this->conversation->research->title,
            'sender_name' => $this->conversation->sender ? $this->conversation->sender->name : $this->conversation->guest_name,
            'subject' => $this->conversation->subject,
            'message' => $this->conversation->latestMessage?->body,
            'link' => route('dashboard.conversations.show', $this->conversation),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        $senderName = $this->conversation->sender ? $this->conversation->sender->name : $this->conversation->guest_name;

        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'id' => $this->id,
            'type' => static::class,
            'title' => 'New Inquiry Received',
            'message' => $senderName.' sent an inquiry: "'.$this->conversation->subject.'"',
            'action_url' => route('dashboard.conversations.show', $this->conversation),
            'created_at' => now()->toIso8601String(),
            'formatted_time' => now()->diffForHumans(),
        ]);
    }
}
