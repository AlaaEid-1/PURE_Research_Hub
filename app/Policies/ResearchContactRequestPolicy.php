<?php

namespace App\Policies;

use App\Models\ResearchContactRequest;
use App\Models\User;

class ResearchContactRequestPolicy
{
    /**
     * Determine whether the user can view any contact requests.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the specific contact inquiry conversation.
     */
    public function view(User $user, ResearchContactRequest $contactRequest): bool
    {
        return (int) $user->id === (int) $contactRequest->sender_id 
            || (int) $user->id === (int) $contactRequest->research?->user_id 
            || $user->isAdmin();
    }

    /**
     * Determine whether the user can reply to the contact inquiry.
     */
    public function reply(User $user, ResearchContactRequest $contactRequest): bool
    {
        return (int) $user->id === (int) $contactRequest->sender_id 
            || (int) $user->id === (int) $contactRequest->research?->user_id;
    }

    /**
     * Determine whether the user can mark the contact inquiry as closed.
     */
    public function close(User $user, ResearchContactRequest $contactRequest): bool
    {
        return (int) $user->id === (int) $contactRequest->sender_id 
            || (int) $user->id === (int) $contactRequest->research?->user_id 
            || $user->isAdmin();
    }
}
