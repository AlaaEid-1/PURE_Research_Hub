<?php

namespace App\Http\Controllers;

use App\Models\ResearchAccessRequest;
use App\Services\AccessRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardAccessRequestController extends Controller
{
    public function __construct(
        protected AccessRequestService $accessRequestService
    ) {}

    /**
     * Display a listing of access requests received for papers owned by the user.
     */
    public function index(Request $request): View
    {
        $requests = ResearchAccessRequest::with(['research', 'requester'])
            ->whereHas('research', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->latest()
            ->paginate(15);

        return view('dashboard.requests.index', compact('requests'));
    }

    /**
     * Approve the specified access request.
     */
    public function approve(ResearchAccessRequest $accessRequest): RedirectResponse
    {
        Gate::authorize('update', $accessRequest);

        $this->accessRequestService->approveRequest($accessRequest);

        return back()->with('success', 'PDF Access Request approved successfully.');
    }

    /**
     * Reject the specified access request.
     */
    public function reject(ResearchAccessRequest $accessRequest): RedirectResponse
    {
        Gate::authorize('update', $accessRequest);

        $this->accessRequestService->rejectRequest($accessRequest);

        return back()->with('success', 'PDF Access Request rejected.');
    }
}
