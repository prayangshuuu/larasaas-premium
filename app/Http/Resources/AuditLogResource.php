<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => (int) $this->id,
            'actor_id'     => $this->actor_id ? (int) $this->actor_id : null,

            // If your AuditLog model defines `actor()` relation, this will
            // only be included when you eager-load it: with('actor')
            'actor'        => $this->whenLoaded('actor', fn () => new UserResource($this->actor)),

            'target_type'  => $this->target_type ?? null,
            'target_id'    => $this->target_id ? (int) $this->target_id : null,

            'action'       => (string) $this->action,
            'description'  => $this->description ?? null,

            'ip_address'   => $this->ip_address ?? null,
            'user_agent'   => $this->user_agent ?? null,

            // Metadata normalized to array/object when JSON, otherwise raw string/null
            'metadata'     => $this->normalizeMetadata($this->metadata),

            'created_at'   => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }

    /**
     * Metadata may be stored as array/object, JSON string, "null", or plain text.
     * Normalize to a PHP array/object when possible.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function normalizeMetadata($value)
    {
        if (is_array($value) || is_object($value)) {
            return $value;
        }

        if (is_string($value)) {
            $trim = trim($value);

            if ($trim === '' || strtolower($trim) === 'null') {
                return null;
            }

            if (in_array($trim[0], ['{', '['], true)) {
                try {
                    $decoded = json_decode($trim, true, 512, JSON_THROW_ON_ERROR);
                    return $decoded;
                } catch (\Throwable) {
                    // fall through to raw string below
                }
            }

            return $value; // plain string (non-JSON)
        }

        return $value; // null or scalar
    }
}
