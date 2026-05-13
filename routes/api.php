<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Middleware\RequireAuth;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    // Zona Publik
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/posts',           [PostController::class,    'index']); 
    Route::get('/posts/{post}',    [PostController::class,    'show']); 
    Route::get('/comments',        [CommentController::class, 'index']); 
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    
    // Zona Privat (Wajib Bearer Token)
    Route::middleware(RequireAuth::class)->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/posts',          [PostController::class, 'store']);
        Route::patch('/posts/{post}',  [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);

        Route::post('/comments',             [CommentController::class, 'store']);
        Route::patch('/comments/{comment}',  [CommentController::class, 'update']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    });
});