<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Get authenticated user's profile
    Route::get('/profile', [UserProfileController::class, 'show']);

    // Update authenticated user's profile
    Route::put('/profile', [UserProfileController::class, 'update']);

    // Change authenticated user's password
    Route::put('/change-password', [UserProfileController::class, 'changePassword']);

    // Logout and revoke current Sanctum token
    Route::post('/logout', [AuthController::class, 'logout']);

    // Delete authenticated user's account
    Route::delete('/profile', [UserProfileController::class, 'destroy']);
});

