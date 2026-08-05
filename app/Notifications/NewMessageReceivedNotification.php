<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewMessageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Conversation $conversation,
        public Message $message
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
        $senderName = $this->message->sender ? $this->message->sender->name : ($this->conversation->guest_name ?: 'Researcher');

        // Check if recipient is a guest or registered user
        if ($this->conversation->sender_id === null && $this->conversation->guest_email === $notifiable->email) {
            $url = URL::signedRoute('guest.conversations.show', ['conversation' => $this->conversation->id]);
        } else {
            $url = route('dashboard.conversations.show', $this->conversation);
        }

        return (new MailMessage)
            ->subject('New Message in Inquiry: '.$this->conversation->subject)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($senderName.' replied to the conversation regarding "'.$this->conversation->research->title.'".')
            ->line('Reply: "'.$this->message->body.'"')
            ->action('View & Reply to Message', $url)
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
            'message_id' => $this->message->id,
            'research_id' => $this->conversation->research_id,
            'research_title' => $this->conversation->research->title,
            'sender_name' => $this->message->sender ? $this->message->sender->name : ($this->conversation->guest_name ?: 'Researcher'),
            'message' => $this->message->body,
            'link' => route('dashboard.conversations.show', $this->conversation),
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): \Illuminate\Notifications\Messages\BroadcastMessage
    {
        $senderName = $this->message->sender ? $this->message->sender->name : ($this->conversation->guest_name ?: 'Researcher');

        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'id' => $this->id,
            'type' => static::class,
            'title' => 'New Reply Received',
            'message' => $senderName.' replied: "'.$this->message->body.'"',
            'action_url' => route('dashboard.conversations.show', $this->conversation),
            'created_at' => now()->toIso8601String(),
            'formatted_time' => now()->diffForHumans(),
        ]);
    }
}
