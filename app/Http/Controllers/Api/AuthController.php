<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }

    /**
     * Login user and generate Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Last Login Information
        |--------------------------------------------------------------------------
        */

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }

    /**
     * Logout the authenticated user.
     *
     * Revokes only the current token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful. Token has been revoked.',
        ]);
    }

    /**
     * Logout from all devices.
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out from all devices successfully.',
        ]);
    }

    /**
     * Get active sessions/devices.
     */
    public function sessions(Request $request)
    {
        $user = $request->user();

        $tokens = $user->tokens()
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Active sessions retrieved successfully.',
            'data' => $tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'created_at' => $token->created_at,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                ];
            }),
        ]);
    }

    /**
     * Revoke a specific device/session token.
     */
    public function revokeSession(Request $request, $tokenId)
    {
        $user = $request->user();

        $token = $user->tokens()
            ->where('id', $tokenId)
            ->first();

        if (! $token) {
            return response()->json([
                'status' => false,
                'message' => 'Session not found.',
            ], 404);
        }

        $token->delete();

        return response()->json([
            'status' => true,
            'message' => 'Session revoked successfully.',
        ]);
    }
}