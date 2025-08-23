<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Compute admin flag safely (prefer model method, fallback to column)
        $isAdmin = method_exists($this->resource, 'isAdmin')
            ? (bool) $this->resource->isAdmin()
            : (bool) ($this->resource->is_admin ?? false);

        // Compute banned flag safely (prefer model method)
        $isBanned = method_exists($this->resource, 'isBanned')
            ? (bool) $this->resource->isBanned()
            : (bool) (!is_null($this->resource->banned_at ?? null));

        // Resolve avatar URL if you store file paths on the "public" disk
        $profilePath = $this->profile_picture ?: null;
        $avatarUrl = $profilePath
            ? (Storage::disk('public')->exists($profilePath)
                ? Storage::url($profilePath)
                : (str_starts_with($profilePath, 'http') ? $profilePath : url($profilePath)))
            : asset('images/default-avatar.png');

        return [
            'id'                => (int) $this->id,
            'name'              => (string) $this->name,
            'username'          => $this->username ? (string) $this->username : null,
            'email'             => (string) $this->email,

            'is_admin'          => $isAdmin,
            'banned'            => $isBanned,
            'banned_at'         => $this->banned_at ? $this->banned_at->toIso8601String() : null,

            'email_verified'    => (bool) !is_null($this->email_verified_at),
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null,

            'avatar_url'        => $avatarUrl,
            'profile_picture'   => $profilePath, // raw stored path (if you need it)

            'created_at'        => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'        => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
