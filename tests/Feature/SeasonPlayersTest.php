<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Season;
use App\Models\Team;
use App\Models\Player;
use App\Models\PlayerTeam;
use Illuminate\Support\Str;

class SeasonPlayersTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deleted_player_team_assignments_are_not_shown()
    {
        // create season (assign id before saving so ULID primary key is set)
        $season = new Season([
            'name' => 'Test Season',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
        ]);
        $season->id = (string) Str::ulid();
        $season->save();

        // create team
        $team = new Team();
        // assign attributes directly to avoid fillable restrictions in the model
        $team->id = (string) Str::ulid();
        $team->name = 'Tigers';
        $team->abbreviation = 'TIG';
        $team->slug = 'tigers';
        $team->city = 'Testville';
        $team->state = 'TS';
        $team->country = 'USA';
        $team->season_id = $season->id;
        $team->save();

        // create two players
    $playerA = new Player();
    $playerA->id = (string) Str::ulid();
    $playerA->name = 'Active Player';
    $playerA->phone = '9';
    $playerA->save();

    $playerB = new Player();
    $playerB->id = (string) Str::ulid();
    $playerB->name = 'Deleted Player';
    $playerB->phone = '10';
    $playerB->save();

        // assign both players to the team (playerTeams)
        $assignA = new PlayerTeam([
            'player_id' => $playerA->id,
            'team_id' => $team->id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);
        $assignA->id = (string) Str::ulid();
        $assignA->save();

        $assignB = new PlayerTeam([
            'player_id' => $playerB->id,
            'team_id' => $team->id,
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);
        $assignB->id = (string) Str::ulid();
        $assignB->save();

        // soft-delete the second assignment
        $assignB->delete();

        // visit season show
        $response = $this->get(route('seasons.show', $season));

        $response->assertStatus(200);
        $response->assertSeeText('Active Player');
        $response->assertDontSeeText('Deleted Player');
    }
}
