<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'stripe_price_id',
        'price',
        'currency',
        'interval',
        'features',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = \Illuminate\Support\Str::slug($plan->name);
            }
        });

        static::updating(function ($plan) {
            if ($plan->isDirty('name') && empty($plan->slug)) {
                $plan->slug = \Illuminate\Support\Str::slug($plan->name);
            }
        });
    }

    /**
     * Helper to get a specific feature limit.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getFeatureLimit(string $key, $default = null)
    {
        $features = $this->features ?? [];
        return $features[$key] ?? $default;
    }
}
