<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;


class RequireAuth
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token && Cache::has('bl_' . $token)) {
            return response()->json(['message' => 'Token telah dicabut (blacklisted).'], 401);
        }

        auth()->shouldUse('sanctum');
        if (!auth('sanctum')->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
