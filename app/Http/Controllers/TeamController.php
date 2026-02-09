<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teams = $request->user()->allTeams();
        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $team = $request->user()->ownedTeams()->create([
            'name'          => $validated['name'],
            'personal_team' => false,
        ]);

        // Switch to the new team immediately
        $request->user()->switchTeam($team);

        return redirect()->route('teams.show', $team)->with('status', 'Team created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Team $team)
    {
        if (! $request->user()->belongsToTeam($team)) {
            abort(403);
        }

        // Switch context to this team when viewing it
        if (! $request->user()->isCurrentTeam($team)) {
            $request->user()->switchTeam($team);
        }

        return view('teams.show', [
            'team' => $team,
            'permissions' => [
                'canAddTeamMembers'    => $request->user()->ownsTeam($team),
                'canDeleteTeam'        => $request->user()->ownsTeam($team),
                'canRemoveTeamMembers' => $request->user()->ownsTeam($team),
                'canUpdateTeam'        => $request->user()->ownsTeam($team),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        if (! $request->user()->ownsTeam($team)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $team->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('teams.show', $team)->with('status', 'Team updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Team $team)
    {
        if (! $request->user()->ownsTeam($team)) {
            abort(403);
        }

        if ($team->personal_team) {
            return back()->with('error', 'You cannot delete your personal team.');
        }

        $team->delete();

        // Switch to personal team
        $personalTeam = $request->user()->personalTeam();
        if ($personalTeam) {
            $request->user()->switchTeam($personalTeam);
        }

        return redirect()->route('dashboard')->with('status', 'Team deleted successfully!');
    }
}
