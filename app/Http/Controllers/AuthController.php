<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    //register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Email atau password Anda salah.'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token',
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => [
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'user_id'    => JWTAuth::user()->id
            ]
        ], 200);
    }


    // logout
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            try {
                // 1. Ambil durasi masa aktif token dari konfigurasi
                $ttlMinutes = (int) config('jwt.ttl', 1440);

                // 2. Simpan ke Cache (Redis) untuk pengecekan manual di Middleware RequireAuth
                Cache::put('bl_' . $token, true, now()->addMinutes($ttlMinutes));

                // 3. Invalidate token secara internal di library JWT
                JWTAuth::invalidate(JWTAuth::getToken());

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Logout berhasil. Token telah masuk daftar hitam (Blacklisted).'
                ], 200);
            } catch (JWTException $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal memproses logout, token mungkin sudah tidak valid.'
                ], 500);
            }
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Token tidak ditemukan dalam permintaan.'
        ], 400);
    }
}
