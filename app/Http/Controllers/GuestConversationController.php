<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestConversationController extends Controller
{
    public function __construct(
        protected ConversationService $conversationService
    ) {}

    /**
     * Display conversation thread for signed guest link.
     */
    public function show(Request $request, Conversation $conversation): View
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Invalid or expired conversation access link.');
        }

        $conversation->load(['research', 'author', 'sender', 'messages.sender']);

        return view('guest.conversations.show', compact('conversation'));
    }

    /**
     * Store guest reply message in conversation thread.
     */
    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Invalid or expired conversation access link.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:3000'],
        ]);

        $this->conversationService->sendMessage(
            $conversation,
            null,
            $validated['body']
        );

        return back()->with('success', 'Your reply message has been sent.');
    }
}
