<?php

namespace App\Services;

use App\Enums\AccessRequestStatus;
use App\Models\Research;
use App\Models\ResearchAccessRequest;
use App\Models\User;
use App\Notifications\AccessRequestDecisionNotification;
use App\Notifications\AccessRequestReceivedNotification;

class AccessRequestService
{
    /**
     * Submit a new access request for a research paper.
     */
    public function createRequest(User $requester, Research $research, string $message): ResearchAccessRequest
    {
        $accessRequest = ResearchAccessRequest::updateOrCreate(
            [
                'research_id' => $research->id,
                'requester_id' => $requester->id,
            ],
            [
                'message' => $message,
                'status' => AccessRequestStatus::PENDING,
            ]
        );

        // Notify paper author
        if ($research->user) {
            $research->user->notify(new AccessRequestReceivedNotification($accessRequest));
        }

        return $accessRequest;
    }

    /**
     * Approve a pending access request.
     */
    public function approveRequest(ResearchAccessRequest $accessRequest): ResearchAccessRequest
    {
        $accessRequest->update([
            'status' => AccessRequestStatus::APPROVED,
        ]);

        // Notify requesting user
        $accessRequest->requester->notify(new AccessRequestDecisionNotification($accessRequest));

        return $accessRequest;
    }

    /**
     * Reject a pending access request.
     */
    public function rejectRequest(ResearchAccessRequest $accessRequest): ResearchAccessRequest
    {
        $accessRequest->update([
            'status' => AccessRequestStatus::REJECTED,
        ]);

        // Notify requesting user
        $accessRequest->requester->notify(new AccessRequestDecisionNotification($accessRequest));

        return $accessRequest;
    }
}
