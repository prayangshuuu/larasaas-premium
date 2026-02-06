<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'role',             // legacy support (fallback for older rows)
        'password',
        'profile_picture',
        'is_admin',
        'email_verified_at', // Allow admin to set this
        'banned_at',        // admin ban/unban
    ];

    /**
     * Hidden attributes for arrays / JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'two_factor_confirmed_at'  => 'datetime',
            'banned_at'                => 'datetime',
            'is_admin'                 => 'boolean',
            'password'                 => 'hashed', // Laravel hashing cast
        ];
    }

    /**
     * Normalize email (lowercase + trim).
     */
    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = is_null($value) ? null : Str::lower(trim($value));
    }

    /**
     * Normalize username (lowercase + trim).
     */
    public function setUsernameAttribute(?string $value): void
    {
        $this->attributes['username'] = is_null($value) ? null : Str::lower(trim($value));
    }

    /**
     * True when the user is an admin.
     * Prefers boolean column; falls back to legacy 'role' column.
     */
    public function isAdmin(): bool
    {
        if (! is_null($this->is_admin)) {
            return (bool) $this->is_admin;
        }

        return isset($this->role) && $this->role === 'admin';
    }

    /**
     * Ban state.
     */
    /**
     * Ban state.
     */
    public function isBanned(): bool
    {
        return ! is_null($this->banned_at);
    }

    /**
     * Get the profile completeness percentage.
     */
    protected function profileCompleteness(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (mixed $value, array $attributes) {
                $progress = 0;
                if (!empty($attributes['name'])) $progress += 33;
                if (!empty($attributes['email_verified_at'])) $progress += 33;
                if (!empty($attributes['profile_picture'])) $progress += 34;
                return $progress;
            }
        );
    }

    /**
     * Get the IELTS stats (Reading, Writing, Listening, Speaking).
     * Currently returns a structured array (DTO-like) to be replaced by a Relationship later.
     */
    protected function ieltsStats(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => [
                'reading'   => ['score' => 7.5, 'progress' => 75, 'desc' => '12 tests completed'],
                'writing'   => ['score' => 6.5, 'progress' => 65, 'desc' => '8 essays submitted'],
                'listening' => ['score' => 8.0, 'progress' => 80, 'desc' => '15 tests completed'],
                'speaking'  => ['score' => 7.0, 'progress' => 70, 'desc' => '6 mock interviews'],
            ]
        );
    }

    /* -----------------------------------------------------------------
     | Query Scopes
     |-----------------------------------------------------------------*/

    /**
     * Scope: admins (supports legacy role column).
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('is_admin', true)
                ->orWhere('role', 'admin');
        });
    }

    /**
     * Scope: banned users only.
     */
    public function scopeBanned(Builder $query): Builder
    {
        return $query->whereNotNull('banned_at');
    }

    /**
     * Scope: not banned users.
     */
    public function scopeNotBanned(Builder $query): Builder
    {
        return $query->whereNull('banned_at');
    }
}
