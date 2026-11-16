<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Kontrak GET /health (PRD 2.7):
 *  - 200 {"status":"ok","database":"connected","timestamp":...} bila DB terhubung
 *  - 503 {"status":"error","database":"disconnected",...} bila DB tidak terjangkau
 * Dipakai stage Smoke Test Jenkins, HEALTHCHECK Dockerfile, dan test otomatis.
 */
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::connection()->getPdo();

            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('[health] database tidak terjangkau: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'database' => 'disconnected',
                'timestamp' => now()->toIso8601String(),
            ], 503);
        }
    }
}
