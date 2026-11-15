<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MetricsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->path() !== 'metrics') {
            $method = $request->method();
            $status = (string) $response->getStatusCode();
            $key = "{$method}_{$status}";

            $counts = Cache::store('file')->get('prom_http_requests_map', []);
            $counts[$key] = [
                'method' => $method,
                'status' => $status,
                'count'  => ($counts[$key]['count'] ?? 0) + 1,
            ];
            Cache::store('file')->forever('prom_http_requests_map', $counts);
        }

        return $response;
    }
}