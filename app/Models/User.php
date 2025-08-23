<?php

namespace App\Models;

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
        'banned_at',        // admin ban/unban
    ];

    /**
     * Hidden attributes for serialization.
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
            'password'                 => 'hashed',
        ];
    }

    /**
     * Normalize email and username (Fortify config expects lowercase).
     */
    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = is_null($value) ? null : Str::lower(trim($value));
    }

    public function setUsernameAttribute(?string $value): void
    {
        // Keep exact value but lowercase for consistency
        $this->attributes['username'] = is_null($value) ? null : Str::lower(trim($value));
    }

    /**
     * Prefer boolean column; fall back to legacy role.
     */
    public function isAdmin(): bool
    {
        // If the boolean column exists and is set, trust it.
        if (!is_null($this->is_admin)) {
            return (bool) $this->is_admin;
        }

        // Fallback for older rows that only have 'role'
        return isset($this->role) && $this->role === 'admin';
    }

    /**
     * Ban state.
     */
    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    /**
     * Handy scopes.
     */
    public function scopeAdmins($query)
    {
        return $query->where(function ($q) {
            $q->where('is_admin', true)
                ->orWhere('role', 'admin'); // legacy fallback
        });
    }

    public function scopeBanned($query)
    {
        return $query->whereNotNull('banned_at');
    }

    public function scopeNotBanned($query)
    {
        return $query->whereNull('banned_at');
    }
}
