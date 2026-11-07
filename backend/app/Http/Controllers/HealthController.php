<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    public function check(Request $request)
    {
        $status = 'healthy';
        $statusCode = 200;
        $checks = [
            'database' => 'ok',
            'cache'    => 'ok',
        ];

        // 1. Cek Basis Data
        try {
            // Pakai raw PDO connection check agar tidak memicu event listener Laravel yang butuh DB
            DB::purge();
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $statusCode = 503;
            $checks['database'] = 'failed: ' . $e->getMessage();

            // Log tanpa menyentuh model/user DB
            try {
                Log::error('Healthcheck Error DB: ' . $e->getMessage(), [
                    'action' => 'DATABASE_CHECK_FAILED'
                ]);
            } catch (\Throwable $logErr) {
                // Abaikan jika log gagal agar tidak memicu HTTP 500
            }
        }

        // 2. Cek Cache (Driver File)
        try {
            Cache::store('file')->put('health_test', 'ok', 5);
            if (Cache::store('file')->get('health_test') !== 'ok') {
                throw new \Exception('Cache test failed');
            }
        } catch (\Throwable $e) {
            $status = 'unhealthy';
            $statusCode = 503;
            $checks['cache'] = 'failed: ' . $e->getMessage();
        }

        return response()->json([
            'status'    => $status,
            'timestamp' => now()->toIso8601String(),
            'checks'    => $checks,
        ], $statusCode);
    }
}