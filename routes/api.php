<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes (Tanpa Autentikasi)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected Routes (Authenticated Users)
Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
});

// Admin Routes (Hanya Admin yang Bisa Akses)
Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'index']);
    Route::delete('/posts/{id}', [AdminController::class, 'deletePost']);
    Route::delete('/comments/{id}', [AdminController::class, 'deleteComment']);
    Route::post('/categories', [CategoryController::class, 'store']); // FIXED: create -> store
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// Fallback Route untuk menangani rute yang tidak ditemukan
Route::fallback(function () {
    return response()->json(['message' => 'Rute tidak ditemukan'], 404);
});
