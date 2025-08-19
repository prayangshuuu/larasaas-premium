<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     * This method is designed to handle partial updates from separate forms.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Use validated() to fill only the fields that were submitted and passed validation.
        $request->user()->fill($request->validated());

        // If the email field was part of the update, reset the verification status.
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Handle the profile picture upload if a new file is present.
        if ($request->hasFile('profile_picture')) {
            // Delete the old picture if it exists to save storage space.
            if ($request->user()->profile_picture && Storage::disk('public')->exists($request->user()->profile_picture)) {
                Storage::disk('public')->delete($request->user()->profile_picture);
            }
            // Store the new picture and update the user's record.
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $request->user()->profile_picture = $path;
        }

        // Save all the changes to the database.
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Also delete the user's profile picture from storage.
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
