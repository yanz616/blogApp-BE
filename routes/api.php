<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes for authentication, admin, and user actions.
|
*/

// Public Routes (Tanpa Autentikasi)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected Routes (Authenticated Users)
Route::middleware(['auth:api'])->group(function () {
    Route::get('me',        [AuthController::class, 'me']);
    Route::post('refresh',  [AuthController::class, 'refresh']);  // Gunakan POST untuk refresh token
    Route::post('logout',   [AuthController::class, 'logout']);   // Gunakan POST untuk logout
});

// Admin Routes (Hanya Admin yang Bisa Akses)
Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
    Route::delete('/posts/{id}', [AdminController::class, 'deletePost']);
    Route::delete('/comments/{id}', [AdminController::class, 'deleteComment']);
    Route::get('/users', [AdminController::class, 'index']);
});
