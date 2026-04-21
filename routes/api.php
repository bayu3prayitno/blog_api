<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('throttle:5,1')->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::get('/comments/{comment}', [CommentController::class, 'show'])->name('comments.show');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);
    Route::apiResource('comments', CommentController::class)->except(['index', 'show']);
});