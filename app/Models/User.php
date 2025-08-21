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

    protected $fillable = [
        'name',
        'username',
        'email',
        'role',          // legacy support
        'password',
        'profile_picture',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin'          => 'boolean',
            'password'          => 'hashed',
        ];
    }

    /**
     * Prefer boolean column, fall back to legacy role.
     */
    public function isAdmin(): bool
    {
        if (!is_null($this->is_admin)) {
            return (bool) $this->is_admin;
        }
        return isset($this->role) && $this->role === 'admin';
    }

    /**
     * Optional helper for queries (supports both schemas).
     */
    public function scopeAdmins($query)
    {
        return $query->where(function ($q) {
            $q->where('is_admin', true)->orWhere('role', 'admin');
        });
    }
}
