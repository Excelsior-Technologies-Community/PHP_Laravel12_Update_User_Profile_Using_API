<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'Profile retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar_url,
                'initials' => $user->initials,
                'avatar_bg_color' => $user->avatar_bg_color,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'city' => $user->city,
                'country' => $user->country,
                'website' => $user->website,
                'github_profile' => $user->github_profile,
                'twitter_profile' => $user->twitter_profile,
                'timezone' => $user->timezone,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'github_profile' => 'nullable|string|max:255',
            'twitter_profile' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:50',
        ]);

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'city' => $user->city,
                'country' => $user->country,
                'website' => $user->website,
                'github_profile' => $user->github_profile,
                'twitter_profile' => $user->twitter_profile,
                'timezone' => $user->timezone,
            ],
        ]);
    }

    /**
     * Upload or update profile avatar image.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'status' => true,
            'message' => 'Profile avatar uploaded successfully',
            'data' => [
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar_url,
                'initials' => $user->initials,
            ],
        ]);
    }

    /**
     * Delete user profile avatar.
     */
    public function destroyAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json([
            'status' => true,
            'message' => 'Profile avatar removed successfully',
            'data' => [
                'avatar_url' => null,
                'initials' => $user->initials,
                'avatar_bg_color' => $user->avatar_bg_color,
            ],
        ]);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|different:current_password',
            'new_password_confirmation' => 'required|string|same:new_password',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Delete the authenticated user's account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successfully.',
        ]);
    }

    /**
     * 1. Get profile completion percentage.
     */
    public function completion(Request $request)
    {
        $user = $request->user();

        $fields = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'avatar' => !empty($user->avatar),
            'phone' => !empty($user->phone),
            'bio' => !empty($user->bio),
            'location' => !empty($user->city) || !empty($user->country),
            'social_links' => !empty($user->website) || !empty($user->github_profile) || !empty($user->twitter_profile),
            'email_verified' => !empty($user->email_verified_at),
        ];

        $completed = collect($fields)->filter()->count();
        $total = count($fields);

        $percentage = round(($completed / $total) * 100);

        return response()->json([
            'status' => true,
            'message' => 'Profile completion calculated successfully.',
            'data' => [
                'percentage' => $percentage,
                'completed_fields' => $completed,
                'total_fields' => $total,
                'fields' => $fields,
            ],
        ]);
    }

    /**
     * 2. Get last login information.
     */
    public function lastLogin(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'Last login information retrieved successfully.',
            'data' => [
                'last_login_at' => $user->last_login_at,
                'last_login_ip' => $user->last_login_ip,
                'last_login_user_agent' => $user->last_login_user_agent,
            ],
        ]);
    }

    /**
     * 3. Change email address.
     */
    public function changeEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'new_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password is incorrect.',
            ], 422);
        }

        $user->update([
            'email' => $request->new_email,
            'email_verified_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Email address changed successfully.',
            'data' => [
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ],
        ]);
    }

    /**
     * 4. Password strength checker.
     */
    public function passwordStrength(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $password = $request->password;

        $score = 0;
        $checks = [];

        if (strlen($password) >= 8) {
            $score++;
            $checks['minimum_length'] = true;
        } else {
            $checks['minimum_length'] = false;
        }

        if (preg_match('/[A-Z]/', $password)) {
            $score++;
            $checks['uppercase'] = true;
        } else {
            $checks['uppercase'] = false;
        }

        if (preg_match('/[a-z]/', $password)) {
            $score++;
            $checks['lowercase'] = true;
        } else {
            $checks['lowercase'] = false;
        }

        if (preg_match('/[0-9]/', $password)) {
            $score++;
            $checks['number'] = true;
        } else {
            $checks['number'] = false;
        }

        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score++;
            $checks['special_character'] = true;
        } else {
            $checks['special_character'] = false;
        }

        $strength = match (true) {
            $score <= 1 => 'Weak',
            $score <= 3 => 'Medium',
            $score === 4 => 'Strong',
            default => 'Very Strong',
        };

        return response()->json([
            'status' => true,
            'message' => 'Password strength calculated successfully.',
            'data' => [
                'score' => $score,
                'maximum_score' => 5,
                'strength' => $strength,
                'checks' => $checks,
            ],
        ]);
    }

    /**
     * 5. Account statistics.
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'Account statistics retrieved successfully.',
            'data' => [
                'user_id' => $user->id,
                'account_age_days' => $user->created_at
                    ? $user->created_at->diffInDays(now())
                    : 0,
                'account_created_at' => $user->created_at,
                'last_updated_at' => $user->updated_at,
                'email_verified' => !empty($user->email_verified_at),
                'active_sessions' => $user->tokens()->count(),
            ],
        ]);
    }
}
