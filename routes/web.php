<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Fruitcake\LaravelDebugbar\Facades\Debugbar;

Route::get('/', function () {
    return redirect('/posts');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/posts', function () {
    return view('posts.index');
});

Route::get('/posts/create', function () {
    return view('posts.create');
});

Route::get('/posts/{id}', function ($id) {
    return view('posts.show', compact('id'));
});

Route::get('/posts/{id}/edit', function ($id) {
    return view('posts.edit', compact('id'));
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
