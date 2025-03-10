<?php

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| assigned to the "api" middleware group.
|
*/

// Auth Routes (Tanpa Middleware)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Menggunakan Middleware auth:sanctum)
Route::middleware(['auth:sanctum'])->group(function() {

    Route::post('/upload', [UploadController::class, 'upload']);

    // User Routes
    Route::prefix('users')->group(function() {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/update-password', [UserController::class, 'updatePassword']);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('albums')->group(function() {
        Route::get('/', [AlbumController::class, 'index']);
        Route::get('/{id}', [AlbumController::class, 'detail']);
        Route::post('/', [AlbumController::class, 'create']);
        Route::put('/{id}', [AlbumController::class, 'update']);
        Route::delete('/{id}', [AlbumController::class, 'delete']);
    });
});
