<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Team::with(['owner', 'users'])
            ->withCount('users');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        $teams = $query->latest()->paginate(10);

        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['owner', 'users', 'invitations']);
        $members = $team->users()->paginate(10); // Paginate members in case of large teams

        return view('admin.teams.show', compact('team', 'members'));
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::all(['id', 'name', 'email']);
        return view('admin.teams.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $team = \App\Models\Team::create([
            'name' => $validated['name'],
            'user_id' => $validated['user_id'],
            'personal_team' => false,
        ]);

        return redirect()->route('admin.teams.index')->with('status', 'Team created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $users = \App\Models\User::all(['id', 'name', 'email']);
        return view('admin.teams.edit', compact('team', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'user_id' => $validated['user_id'],
        ]);

        return redirect()->route('admin.teams.index')->with('status', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        // Detach all members
        $team->users()->detach();

        // Delete invitations
        $team->invitations()->delete();

        // Delete the team
        $team->delete();

        return redirect()->route('admin.teams.index')->with('status', 'Team deleted successfully.');
    }

    /**
     * Remove a member from the team (Admin action).
     */
    public function removeMember(Request $request, Team $team, \App\Models\User $user)
    {
        // Prevent removing the owner
        if ($team->owner->id === $user->id) {
             return back()->with('error', 'Cannot remove the team owner.');
        }

        $team->users()->detach($user);

        return back()->with('status', 'Team member removed.');
    }
}
