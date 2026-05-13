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

        // 1. Validasi keberadaan token [cite: 172]
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 2. Cek Daftar Hitam di Redis sesuai implementasi logout [cite: 141, 176]
        if (Cache::has('bl_' . $token)) {
            return response()->json([
                'message' => 'Akses ditolak. Token ini telah masuk daftar hitam (Blacklisted).',
                'error' => 'Unauthorized'
            ], 401);
        }

        // 3. Gunakan guard 'api' (JWT) bukan 'sanctum' [cite: 66, 183]
        auth()->shouldUse('api');

        try {
            // 4. Authenticate user menggunakan JWTAuth 
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        } catch (TokenExpiredException $e) {
            // Memberikan pesan spesifik sesuai standar praktikum [cite: 191]
            return response()->json(['message' => 'Token sudah expired.'], 401);
        } catch (JWTException $e) {
            // Menangani token tidak valid secara otomatis [cite: 193]
            return response()->json(['message' => 'Token tidak valid.'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
