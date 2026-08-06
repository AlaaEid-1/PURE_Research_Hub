<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\ResearchDownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ResearchDownloadController extends Controller
{
    /**
     * Handle secure PDF download streaming for research publications.
     */
    public function __invoke(Request $request, Research $research)
    {
        Gate::authorize('download', $research);

        if (! Storage::disk('private_research')->exists($research->pdf_path)) {
            return redirect()->route('research.show', $research->slug)
                ->with('error', 'The requested PDF file is no longer available on the server.');
        }

        $research->increment('downloads');

        // Log analytics download record
        ResearchDownloadLog::create([
            'research_id' => $research->id,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500),
            'downloaded_at' => now(),
        ]);

        $fileName = Str::slug($research->title).'.pdf';
        $absolutePath = Storage::disk('private_research')->path($research->pdf_path);

        return response()->download($absolutePath, $fileName, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, max-age=0',
            'Accept-Ranges' => 'bytes',
            'X-Sendfile' => $absolutePath,
        ]);
    }
}
