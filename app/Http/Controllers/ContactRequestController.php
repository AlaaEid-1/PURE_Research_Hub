<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Research;
use App\Services\ConversationService;
use Illuminate\Http\RedirectResponse;

class ContactRequestController extends Controller
{
    public function __construct(
        protected ConversationService $conversationService
    ) {}

    /**
     * Submit a contact inquiry for a research paper author and start a conversation.
     */
    public function store(StoreContactRequest $request, Research $research): RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->id === $research->user_id) {
            return back()->with('error', 'You are the author of this research paper.');
        }

        $validated = $request->validated();

        $conversation = $this->conversationService->startConversation(
            research: $research,
            sender: $user,
            messageBody: $validated['message'],
            subject: $validated['subject'] ?? null,
            guestName: $validated['guest_name'] ?? null,
            guestEmail: $validated['guest_email'] ?? null
        );

        if ($user) {
            return redirect()->route('dashboard.conversations.show', $conversation)
                ->with('success', 'Your inquiry has been sent to the researcher.');
        }

        return back()->with('success', 'Your inquiry has been sent. Check your email for a secure link to your conversation.');
    }
}
