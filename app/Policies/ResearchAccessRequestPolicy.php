<?php

namespace App\Policies;

use App\Models\ResearchAccessRequest;
use App\Models\User;

class ResearchAccessRequestPolicy
{
    /**
     * Determine whether the user can update/approve/reject the access request.
     */
    public function update(User $user, ResearchAccessRequest $accessRequest): bool
    {
        return $user->id === $accessRequest->research->user_id || $user->isAdmin();
    }
}
