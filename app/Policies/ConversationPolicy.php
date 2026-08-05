<?php

namespace App\Policies;

use App\Enums\ConversationStatus;
use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Determine whether the user can view any conversations.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the specific conversation thread.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return (int) $user->id === (int) $conversation->author_id
            || (int) $user->id === (int) $conversation->sender_id
            || $user->isAdmin();
    }

    /**
     * Determine whether the user can reply to the conversation thread.
     */
    public function reply(User $user, Conversation $conversation): bool
    {
        if ($conversation->status === ConversationStatus::CLOSED) {
            return false;
        }

        return (int) $user->id === (int) $conversation->author_id
            || (int) $user->id === (int) $conversation->sender_id;
    }

    /**
     * Determine whether the user can grant PDF download access.
     */
    public function grantAccess(User $user, Conversation $conversation): bool
    {
        return (int) $user->id === (int) $conversation->author_id
            || $user->isAdmin();
    }

    /**
     * Determine whether the user can close the conversation.
     */
    public function close(User $user, Conversation $conversation): bool
    {
        return (int) $user->id === (int) $conversation->author_id
            || (int) $user->id === (int) $conversation->sender_id
            || $user->isAdmin();
    }
}
