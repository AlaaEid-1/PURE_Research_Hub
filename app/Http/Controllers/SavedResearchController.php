<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\SavedResearch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedResearchController extends Controller
{
    /**
     * Display authenticated user's saved/bookmarked research collection.
     */
    public function index(Request $request): View
    {
        $researches = $request->user()
            ->savedResearches()
            ->with(['user', 'category', 'authors'])
            ->latest('saved_researches.created_at')
            ->paginate(12);

        return view('dashboard.bookmarks', compact('researches'));
    }

    /**
     * Toggle saving/bookmarking a research paper.
     */
    public function toggle(Request $request, Research $research): RedirectResponse
    {
        $user = $request->user();

        $saved = SavedResearch::where('user_id', $user->id)
            ->where('research_id', $research->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $msg = 'Research paper removed from your bookmarks.';
        } else {
            SavedResearch::create([
                'user_id' => $user->id,
                'research_id' => $research->id,
            ]);
            $msg = 'Research paper saved to your bookmarks collection.';
        }

        return back()->with('success', $msg);
    }
}
