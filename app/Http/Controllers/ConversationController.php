<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationService $conversationService
    ) {}

    /**
     * Display a listing of incoming research inquiries (for authors) and sent inquiries (for senders).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Inquiries received on papers owned by this author
        $receivedConversations = Conversation::where('author_id', $user->id)
            ->with(['research', 'sender', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(10, ['*'], 'received_page');

        // Inquiries sent by this user
        $sentConversations = Conversation::where('sender_id', $user->id)
            ->with(['research', 'author', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(10, ['*'], 'sent_page');

        return view('dashboard.conversations.index', compact('receivedConversations', 'sentConversations'));
    }

    /**
     * Display the conversation thread.
     */
    public function show(Request $request, Conversation $conversation): View
    {
        Gate::authorize('view', $conversation);

        // Mark unread messages as read for authenticated user
        $this->conversationService->markAsRead($conversation, $request->user());

        $conversation->load(['research', 'author', 'sender', 'messages.sender']);

        // Check if user currently has active download access grant
        $hasAccessGrant = \App\Models\ResearchAccessGrant::where('research_id', $conversation->research_id)
            ->where('user_id', $conversation->sender_id)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->exists();

        return view('dashboard.conversations.show', compact('conversation', 'hasAccessGrant'));
    }

    /**
     * Store a reply message in the conversation.
     */
    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        Gate::authorize('reply', $conversation);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:3000'],
        ]);

        $this->conversationService->sendMessage(
            $conversation,
            $request->user(),
            $validated['body']
        );

        return back()->with('success', 'Your reply message has been sent.');
    }

    /**
     * Grant PDF download access to the inquirer.
     */
    public function grantAccess(Request $request, Conversation $conversation): RedirectResponse
    {
        Gate::authorize('grantAccess', $conversation);

        $validated = $request->validate([
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $this->conversationService->grantAccess(
            $conversation,
            $request->user(),
            $validated['expires_in_days'] ?? null
        );

        return back()->with('success', 'Download access has been granted to the researcher.');
    }

    /**
     * Mark the conversation as closed.
     */
    public function close(Request $request, Conversation $conversation): RedirectResponse
    {
        Gate::authorize('close', $conversation);

        $this->conversationService->closeConversation($conversation);

        return back()->with('success', 'Conversation thread has been closed.');
    }
}
