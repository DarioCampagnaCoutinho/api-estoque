<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $services = [
            'app' => [
                'status' => 'running',
            ],
            'database' => $this->databaseStatus(),
            'cache' => [
                'status' => 'configured',
                'driver' => config('cache.default'),
            ],
            'session' => [
                'status' => 'configured',
                'driver' => config('session.driver'),
            ],
            'queue' => [
                'status' => 'configured',
                'driver' => config('queue.default', env('QUEUE_CONNECTION', 'sync')),
            ],
        ];

        $healthy = collect($services)->every(
            fn (array $service) => in_array($service['status'], ['running', 'configured'], true)
        );

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'services' => $services,
            'timestamp' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }

    private function databaseStatus(): array
    {
        try {
            DB::connection()->getPdo();

            return [
                'status' => 'running',
                'connection' => config('database.default'),
                'database' => DB::connection()->getDatabaseName(),
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'down',
                'connection' => config('database.default'),
                'error' => $exception->getMessage(),
            ];
        }
    }
}
