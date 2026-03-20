<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('X-API-Key');

        $map = collect(explode(',', env('API_KEYS', '')))
            ->mapWithKeys(function ($entry) {
                [$alias, $secret] = array_pad(explode(':', trim($entry), 2), 2, '');
                return [$secret => $alias];
            })
            ->filter(fn ($alias, $secret) => $secret !== '' && $alias !== '');

        if (! $header || ! $map->has($header)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        app()->instance('tenant_id', $map->get($header));

        return $next($request);
    }
}
