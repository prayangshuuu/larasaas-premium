<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_subscription_id',
        'status',
        'current_period_end',
    ];

    protected $casts = [
        'current_period_end' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan that owns the subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Determine if the subscription is on a "generic" grace period.
     * In our custom logic, if status is 'canceled' but period end is future, it's on grace period.
     */
    public function onGracePeriod(): bool
    {
        return $this->status === 'canceled' && $this->current_period_end && $this->current_period_end->isFuture();
    }
}
