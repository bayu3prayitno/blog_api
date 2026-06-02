<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class RequireAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        // 1. Validasi keberadaan token
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 2. Cek Daftar Hitam
        if (Cache::has('bl_' . $token)) {
            return response()->json([
                'message' => 'Akses ditolak. Token ini telah masuk daftar hitam (Blacklisted).',
                'error' => 'Unauthorized'
            ], 401);
        }

        try {
            // 3. Authenticate user menggunakan JWTAuth
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token sudah expired.'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}