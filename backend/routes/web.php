<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\HealthController;
Route::get('/health', [HealthController::class, 'check']);

Route::get('/metrics', function () {
    $counts = Cache::store('file')->get('prom_http_requests_map', []);

    $lines = [
        '# HELP http_requests_total Jumlah permintaan HTTP yang diterima aplikasi',
        '# TYPE http_requests_total counter',
    ];

    if (empty($counts)) {
        $lines[] = 'http_requests_total{method="GET",status="200"} 0';
    } else {
        foreach ($counts as $item) {
            $lines[] = sprintf(
                'http_requests_total{method="%s",status="%s"} %d',
                $item['method'],
                $item['status'],
                $item['count']
            );
        }
    }

    return response(implode("\n", $lines) . "\n", 200)
        ->header('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
});