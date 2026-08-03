<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccessRequest;
use App\Models\Research;
use App\Services\AccessRequestService;
use Illuminate\Http\RedirectResponse;

class AccessRequestController extends Controller
{
    public function __construct(
        protected AccessRequestService $accessRequestService
    ) {}

    /**
     * Submit an access request for a research paper.
     */
    public function store(StoreAccessRequest $request, Research $research): RedirectResponse
    {
        if ($request->user()->id === $research->user_id) {
            return back()->with('error', 'You are the author of this research paper.');
        }

        $this->accessRequestService->createRequest(
            $request->user(),
            $research,
            $request->validated('message')
        );

        return back()->with('success', 'Your access request has been submitted to the researcher.');
    }
}
