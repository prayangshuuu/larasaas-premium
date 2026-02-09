<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function ($user) {
            // Auto-create personal team if feature enabled
            if (\App\Helpers\Feature::enabled('team_management_enabled')) {
                $user->createPersonalTeam();
            }
        });
    }

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
    /**
     * Scope: not banned users.
     */
    public function scopeNotBanned(Builder $query): Builder
    {
        return $query->whereNull('banned_at');
    }

    /* -----------------------------------------------------------------
     | Team Management
     |-----------------------------------------------------------------*/

    /**
     * Get the current team for the user.
     */
    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Get all of the teams the user belongs to or owns.
     */
    public function allTeams(): \Illuminate\Support\Collection
    {
        return $this->ownedTeams->merge($this->teams)->sortBy('name');
    }

    /**
     * Get all of the teams the user owns.
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get all of the teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user')
                    ->withPivot('role')
                    ->withTimestamps()
                    ->as('membership');
    }

    /**
     * Determine if the given team is the current team.
     */
    public function isCurrentTeam($team): bool
    {
        return $team->id === $this->current_team_id;
    }

    /**
     * Switch the user's context to the given team.
     */
    public function switchTeam($team): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        return true;
    }

    /**
     * Determine if the user belongs to the given team.
     */
    public function belongsToTeam($team): bool
    {
        if (is_null($team)) {
            return false;
        }

        return $this->ownsTeam($team) || $this->teams->contains(function ($t) use ($team) {
            return $t->id === $team->id;
        });
    }

    /**
     * Determine if the user owns the given team.
     */
    public function ownsTeam($team): bool
    {
        if (is_null($team)) {
            return false;
        }

        return $this->id == $team->user_id;
    }

    /**
     * Create a personal team for the user.
     */
    public function createPersonalTeam(): void
    {
        $team = $this->ownedTeams()->create([
            'name' => explode(' ', $this->name, 2)[0]."'s Team",
            'personal_team' => true,
        ]);

        $this->switchTeam($team);
    }

    /**
     * Get the user's personal team.
     */
    public function personalTeam(): ?Team
    {
        return $this->ownedTeams->where('personal_team', true)->first();
    }

    /* -----------------------------------------------------------------
     | Relationships
     |-----------------------------------------------------------------*/

    /**
     * Get the subscriptions for the user.
     */
    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the invoices for the user.
     */
    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the webhooks for the user.
     */
    public function webhooks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    /**
     * Get usage count for a specific feature key.
     * 
     * @param string $key
     * @return int
     */
    public function getUsage(string $key): int
    {
        // TODO: Implement actual usage tracking logic (e.g., query a usages table)
        // For now, return 0 to allow testing without limits blocking immediately.
        return 0;
    }
}
