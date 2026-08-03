<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Research;
use App\Models\ResearchContactRequest;
use Illuminate\Http\RedirectResponse;

class ContactRequestController extends Controller
{
    /**
     * Submit a contact inquiry for a research paper author.
     */
    public function store(StoreContactRequest $request, Research $research): RedirectResponse
    {
        if ($request->user()->id === $research->user_id) {
            return back()->with('error', 'You are the author of this research paper.');
        }

        ResearchContactRequest::create([
            'research_id' => $research->id,
            'sender_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your inquiry message has been sent to the researcher.');
    }
}
