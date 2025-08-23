<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Require API auth via Sanctum for all actions.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/v1/profile
     * Return the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return (new UserResource($request->user()))
            ->additional(['status' => 'ok']);
    }

    /**
     * PATCH /api/v1/profile
     * Update basic profile fields.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email'    => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->fill($data)->save();

        return (new UserResource($user->fresh()))
            ->additional(['status' => 'ok']);
    }

    /**
     * PATCH /api/v1/profile/password
     * Update password (requires current_password).
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        $user->password = $data['password']; // hashed via model cast
        $user->save();

        return response()->json(['status' => 'ok']);
    }

    /**
     * POST /api/v1/profile/photo
     * Upload/replace avatar. Returns updated user and photo_url.
     */
    public function uploadPhoto(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Delete existing stored avatar if we own the path
        if ($user->profile_picture) {
            if (str_starts_with($user->profile_picture, 'public/')) {
                Storage::delete($user->profile_picture);
            } elseif (str_starts_with($user->profile_picture, 'storage/')) {
                $rel = substr($user->profile_picture, 8); // strip "storage/"
                if ($rel) {
                    Storage::disk('public')->delete($rel);
                }
            }
        }

        $stored = $request->file('photo')->store('avatars', 'public'); // storage/app/public/avatars/...
        $user->profile_picture = 'public/' . ltrim($stored, '/');      // internal path
        $user->save();

        return (new UserResource($user->fresh()))
            ->additional([
                'status'    => 'ok',
                'photo_url' => Storage::url($user->profile_picture),   // public URL
            ]);
    }

    /**
     * DELETE /api/v1/profile/photo
     * Remove avatar and clear profile_picture.
     */
    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_picture) {
            if (str_starts_with($user->profile_picture, 'public/')) {
                Storage::delete($user->profile_picture);
            } elseif (str_starts_with($user->profile_picture, 'storage/')) {
                $rel = substr($user->profile_picture, 8);
                if ($rel) {
                    Storage::disk('public')->delete($rel);
                }
            }
            $user->profile_picture = null;
            $user->save();
        }

        return (new UserResource($user->fresh()))
            ->additional(['status' => 'ok']);
    }
}
