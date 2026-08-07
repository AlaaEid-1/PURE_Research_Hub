<?php

namespace App\Notifications;

use App\Models\ResearchAccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccessRequestDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public ResearchAccessRequest $accessRequest
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
            'access_request_id' => $this->accessRequest->id,
            'research_id' => $this->accessRequest->research_id,
            'research_title' => $this->accessRequest->research?->title,
            'status' => $this->accessRequest->status->value,
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('AccessRequestDecisionNotification failed', [
            'access_request_id' => $this->accessRequest->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
