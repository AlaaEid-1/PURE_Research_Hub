<?php

namespace App\Events;

use App\Models\ResearchContactReply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactReplySent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ResearchContactReply $reply
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('contact-request.'.$this->reply->contact_request_id),
        ];
    }

    /**
     * Data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'reply_id' => $this->reply->id,
            'contact_request_id' => $this->reply->contact_request_id,
            'user_id' => $this->reply->user_id,
            'user_name' => $this->reply->user->name,
            'message' => $this->reply->message,
            'created_at' => $this->reply->created_at->toIso8601String(),
            'formatted_time' => $this->reply->created_at->diffForHumans(),
        ];
    }
}
