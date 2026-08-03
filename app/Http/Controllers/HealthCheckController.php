<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class HealthCheckController extends Controller
{
    /**
     * Determine the health status of the application.
     */
    public function __invoke(): JsonResponse
    {
        $status = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'database' => 'unreachable',
                'queue' => 'unreachable',
            ],
        ];

        $statusCode = 200;

        // Check Database
        try {
            DB::connection()->getPdo();
            $status['services']['database'] = 'ok';
        } catch (\Exception $e) {
            $status['status'] = 'error';
            $statusCode = 503;
        }

        // Check Queue
        try {
            // Check if queue size is readable
            Queue::size();
            $status['services']['queue'] = 'ok';
        } catch (\Exception $e) {
            $status['status'] = 'error';
            $statusCode = 503;
        }

        return response()->json($status, $statusCode);
    }
}
