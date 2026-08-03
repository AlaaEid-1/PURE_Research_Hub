<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\ResearchDownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResearchDownloadController extends Controller
{
    /**
     * Handle secure PDF download streaming for research publications.
     */
    public function __invoke(Request $request, Research $research): StreamedResponse
    {
        Gate::authorize('download', $research);

        if (! Storage::disk('private_research')->exists($research->pdf_path)) {
            abort(404, 'PDF file not found.');
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

        return response()->streamDownload(function () use ($research) {
            $stream = Storage::disk('private_research')->readStream($research->pdf_path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
