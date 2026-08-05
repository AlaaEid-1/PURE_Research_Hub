<?php

namespace App\Http\Controllers;

use App\Models\ResearchContactRequest;
use App\Services\ContactRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardContactRequestController extends Controller
{
    public function __construct(
        protected ContactRequestService $contactRequestService
    ) {}

    /**
     * Display a listing of incoming research inquiries (for authors) and sent inquiries (for senders).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Inquiries received on user's research papers
        $receivedRequests = ResearchContactRequest::whereHas('research', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['research', 'sender'])
        ->latest()
        ->paginate(10, ['*'], 'received_page');

        // Inquiries sent by user to other authors
        $sentRequests = ResearchContactRequest::where('sender_id', $user->id)
        ->with(['research', 'research.user'])
        ->latest()
        ->paginate(10, ['*'], 'sent_page');

        return view('dashboard.inquiries.index', compact('receivedRequests', 'sentRequests'));
    }

    /**
     * Display the conversation thread for a specific research contact inquiry.
     */
    public function show(Request $request, ResearchContactRequest $contactRequest): View
    {
        Gate::authorize('view', $contactRequest);

        $contactRequest->load(['research', 'research.user', 'sender', 'replies.user']);

        return view('dashboard.inquiries.show', compact('contactRequest'));
    }

    /**
     * Send a reply message in the contact inquiry thread.
     */
    public function reply(Request $request, ResearchContactRequest $contactRequest): RedirectResponse
    {
        Gate::authorize('reply', $contactRequest);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $this->contactRequestService->replyToInquiry(
            $contactRequest,
            $request->user(),
            $validated['message']
        );

        return back()->with('success', 'Your reply has been sent successfully.');
    }

    /**
     * Mark the contact inquiry as closed.
     */
    public function close(Request $request, ResearchContactRequest $contactRequest): RedirectResponse
    {
        Gate::authorize('close', $contactRequest);

        $this->contactRequestService->closeInquiry($contactRequest);

        return back()->with('success', 'Inquiry thread has been marked as closed.');
    }
}
