<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeamManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_team()
    {
        // Enable feature
        \App\Helpers\Feature::fake(['team_management_enabled' => true]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('teams.store'), [
            'name' => 'New Team',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('teams', [
            'name' => 'New Team',
            'user_id' => $user->id,
            'personal_team' => false,
        ]);

        $user->refresh();
        $this->assertEquals('New Team', $user->currentTeam->name);
    }

    public function test_user_can_switch_teams()
    {
        \App\Helpers\Feature::fake(['team_management_enabled' => true]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $team1 = $user->ownedTeams()->create(['name' => 'Team 1', 'personal_team' => false]);
        $team2 = $user->ownedTeams()->create(['name' => 'Team 2', 'personal_team' => false]);

        $this->put(route('current-team.update'), ['team_id' => $team1->id]);
        $this->assertEquals($team1->id, $user->fresh()->current_team_id);

        $this->put(route('current-team.update'), ['team_id' => $team2->id]);
        $this->assertEquals($team2->id, $user->fresh()->current_team_id);
    }

    public function test_user_can_add_member()
    {
        \App\Helpers\Feature::fake(['team_management_enabled' => true]);

        $owner = User::factory()->create();
        $member = User::factory()->create();
        $this->actingAs($owner);

        $team = $owner->ownedTeams()->create(['name' => 'My Team', 'personal_team' => false]);
        $owner->switchTeam($team);

        $response = $this->post(route('teams.members.store', $team), [
            'email' => $member->email,
        ]);

        $response->assertRedirect();
        $this->assertTrue($team->users->contains($member));
    }

    public function test_user_cannot_access_teams_if_feature_disabled()
    {
        \App\Helpers\Feature::fake(['team_management_enabled' => false]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('teams.create'));
        $response->assertNotFound();
    }
}
