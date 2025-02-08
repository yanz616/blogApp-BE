<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route untuk user yang sudah login (menggunakan Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Autentikasi pengguna
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Route khusus admin untuk menghapus postingan dan komentar
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::delete('/admin/posts/{id}', [AuthController::class, 'deletePost']);
    Route::delete('/admin/comments/{id}', [AuthController::class, 'deleteComment']);
    Route::get('/admin/users', [AuthController::class, 'index']);
});

// Route yang hanya bisa diakses oleh pengguna yang sudah login
Route::middleware(['auth:api'])->group(function () {
    Route::get('me',        [AuthController::class, 'me']);
    Route::post('refresh',  [AuthController::class, 'refresh']);  // Gunakan POST untuk refresh token
    Route::post('logout',   [AuthController::class, 'logout']);   // Gunakan POST untuk logout
});
