<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'phone',
        'bio',
        'city',
        'country',
        'website',
        'github_profile',
        'twitter_profile',
        'timezone',
        'password',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
        'initials',
        'avatar_bg_color',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get avatar URL if stored.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        return null;
    }

    /**
     * Get uppercase initials for avatar badge fallback.
     */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name ?? ''));
        $initials = '';

        if (!empty($words)) {
            if (count($words) >= 2) {
                $initials = mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1);
            } elseif (count($words) === 1 && !empty($words[0])) {
                $initials = mb_substr($words[0], 0, min(2, mb_strlen($words[0])));
            }
        }

        return strtoupper($initials ?: 'U');
    }

    /**
     * Get consistent background color for initials badge.
     */
    public function getAvatarBgColorAttribute(): string
    {
        $colors = [
            '#3b82f6', // Blue
            '#6366f1', // Indigo
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#ef4444', // Red
            '#f97316', // Orange
            '#10b981', // Emerald
            '#14b8a6', // Teal
            '#06b6d4', // Cyan
        ];

        $hash = crc32($this->email ?: ($this->name ?: 'user'));
        return $colors[abs($hash) % count($colors)];
    }
}
