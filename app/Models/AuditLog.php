<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'actor_id',
        'target_type',
        'target_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The model being acted upon (polymorphic).
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The user who performed the action.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Convenience writer.
     *
     * @param  \Illuminate\Database\Eloquent\Model|array{id:int,type:string}|null  $target
     */
    public static function write(
        mixed $target,
        string $action,
        ?string $description = null,
        array $metadata = []
    ): void {
        $targetType = null;
        $targetId   = null;

        if ($target instanceof Model) {
            $targetType = get_class($target);
            $targetId   = $target->getKey();
        } elseif (is_array($target) && isset($target['type'], $target['id'])) {
            $targetType = (string) $target['type'];
            $targetId   = (int) $target['id'];
        }

        // Be defensive: when running from console there may be no request bound.
        $request = app()->bound('request') ? request() : null;

        static::create([
            'actor_id'    => Auth::id(),
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $request?->ip(),
            'user_agent'  => $request ? Str::limit((string) $request->userAgent(), 1024, '') : null,
            'metadata'    => $metadata ?: null,
        ]);
    }

    /**
     * Scope: newest first.
     */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope: logs for a specific target model.
     */
    public function scopeForTarget($query, Model $model)
    {
        return $query
            ->where('target_type', get_class($model))
            ->where('target_id', $model->getKey());
    }
}
