<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeamMemberController extends Controller
{
    /**
     * Add a new member to the team (via invitation).
     */
    public function store(Request $request, Team $team)
    {
        if (! $request->user()->ownsTeam($team)) {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Check if user is already in the team
        if ($team->users()->where('email', $email)->exists()) {
            return back()->with('error', 'User is already a member of this team.');
        }

        // Check if user exists in the system
        $userToAdd = User::where('email', $email)->first();

        if ($userToAdd) {
            $team->users()->attach($userToAdd, ['role' => 'member']);
            
            return back()->with('status', 'User added to team.');
        }

        // Check if invitation already exists
        if ($team->invitations()->where('email', $email)->exists()) {
            return back()->with('error', 'Invitation already sent to this email.');
        }

        // Create invitation for non-existing user
        $team->invitations()->create([
            'email' => $email,
            'role' => 'member',
            'token' => Str::random(32),
        ]);

        return back()->with('status', 'Invitation sent to ' . $email);
    }

    /**
     * Remove the specified user from the team.
     */
    public function destroy(Request $request, Team $team, User $user)
    {
        if (! $request->user()->ownsTeam($team)) {
            abort(403);
        }

        if ($user->id === $team->owner->id) {
            return back()->with('error', 'You cannot remove yourself from your own team.');
        }

        $team->users()->detach($user);

        return back()->with('status', 'Team member removed.');
    }
}
