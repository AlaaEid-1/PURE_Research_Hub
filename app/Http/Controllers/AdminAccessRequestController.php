<?php

namespace App\Http\Controllers;

use App\Models\ResearchAccessRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAccessRequestController extends Controller
{
    /**
     * Display all access requests across the platform.
     */
    public function index(Request $request): View
    {
        $query = ResearchAccessRequest::with(['research', 'requester'])->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.access-requests.index', compact('requests', 'status'));
    }
}
