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

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    // Get authenticated user's profile
    Route::get('/profile', [
        UserProfileController::class,
        'show'
    ]);

    // Update authenticated user's profile
    Route::put('/profile', [
        UserProfileController::class,
        'update'
    ]);

    // Delete authenticated user's account
    Route::delete('/profile', [
        UserProfileController::class,
        'destroy'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    */

    // Change password
    Route::put('/change-password', [
        UserProfileController::class,
        'changePassword'
    ]);

    // Check password strength
    Route::post('/password-strength', [
        UserProfileController::class,
        'passwordStrength'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    */

    // Change email address
    Route::put('/change-email', [
        UserProfileController::class,
        'changeEmail'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Login / Sessions
    |--------------------------------------------------------------------------
    */

    // Logout current device
    Route::post('/logout', [
        AuthController::class,
        'logout'
    ]);

    // Logout all devices
    Route::post('/logout-all', [
        AuthController::class,
        'logoutAll'
    ]);

    // List active sessions
    Route::get('/sessions', [
        AuthController::class,
        'sessions'
    ]);

    // Revoke specific session
    Route::delete('/sessions/{tokenId}', [
        AuthController::class,
        'revokeSession'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Profile Analytics
    |--------------------------------------------------------------------------
    */

    // Profile completion percentage
    Route::get('/profile/completion', [
        UserProfileController::class,
        'completion'
    ]);

    // Last login information
    Route::get('/profile/last-login', [
        UserProfileController::class,
        'lastLogin'
    ]);

    // Account statistics
    Route::get('/profile/statistics', [
        UserProfileController::class,
        'statistics'
    ]);
});