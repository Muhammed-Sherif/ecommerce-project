<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)->withHeaders($this->buildHeaders($request));
        }

        $response = $next($request);
        foreach ($this->buildHeaders($request) as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }

    private function buildHeaders(Request $request): array
    {
        $origin = $request->headers->get('Origin');
        $allowed = array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173,http://127.0.0.1:5173'))));
        $allowOrigin = $origin && in_array($origin, $allowed, true) ? $origin : ($allowed[0] ?? '*');

        return [
            'Access-Control-Allow-Origin' => $allowOrigin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, X-Tenant, X-Tenant-ID',
            'Access-Control-Allow-Credentials' => 'true',
        ];
    }
}
