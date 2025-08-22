<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'role',            // legacy support
        'password',
        'profile_picture',
        'is_admin',
        'banned_at',       // allow admin ban/unban via mass-update
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
            'email_verified_at' => 'datetime',
            'banned_at'         => 'datetime', // ← cast to Carbon
            'is_admin'          => 'boolean',
            'password'          => 'hashed',
        ];
    }

    /**
     * Prefer boolean column; fall back to legacy role.
     */
    public function isAdmin(): bool
    {
        if (!is_null($this->is_admin)) {
            return (bool) $this->is_admin;
        }
        return isset($this->role) && $this->role === 'admin';
    }

    /**
     * Optional helper to check ban state.
     */
    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    /**
     * Query scope to get admins (supports both schemas).
     */
    public function scopeAdmins($query)
    {
        return $query->where(function ($q) {
            $q->where('is_admin', true)
                ->orWhere('role', 'admin');
        });
    }
}
