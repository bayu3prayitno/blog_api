<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Fruitcake\LaravelDebugbar\Facades\Debugbar;

Route::get('/', function () {
    Debugbar::info('Berhasil! Composer sudah ter-autoload 😁.');
    return view('welcome');
});

// Route Pengujian Redis
Route::get('/test-redis', function () {
    Cache::put('test_key', 'Redis berhasil terhubung pada: ' . now(), 60);

    // Mengambil data dari Redis
    $value = Cache::get('test_key');

    return response()->json([
        'status' => 'success',
        'driver' => config('cache.default'),
        'data_from_redis' => $value,
        'message' => 'Integrasi Redis pada Project Polines Berhasil!'
    ]);
});
