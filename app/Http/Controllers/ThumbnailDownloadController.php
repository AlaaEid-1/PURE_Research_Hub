<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ThumbnailDownloadController extends Controller
{
    /**
     * Stream the thumbnail securely with cache headers.
     */
    public function __invoke(Research $research): StreamedResponse
    {
        if (! $research->thumbnail_path) {
            abort(404, 'Thumbnail not found.');
        }

        $disk = Storage::disk('private_research');

        if (! $disk->exists($research->thumbnail_path)) {
            abort(404, 'Thumbnail file not found on disk.');
        }

        $mimeType = $disk->mimeType($research->thumbnail_path);

        $headers = [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=86400', // Cache for 1 day
        ];

        return response()->stream(function () use ($disk, $research) {
            $stream = $disk->readStream($research->thumbnail_path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }
}
