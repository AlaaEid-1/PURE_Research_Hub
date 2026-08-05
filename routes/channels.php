<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);

    if (! $conversation) {
        return false;
    }

    return (int) $user->id === (int) $conversation->author_id
        || (int) $user->id === (int) $conversation->sender_id
        || $user->isAdmin();
});
