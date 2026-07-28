<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_games_index_returns_collection(): void
    {
        $home = Team::factory()->create();
        $away = Team::factory()->create();

        Game::factory()->create([
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'utc_date' => now(),
            'status' => 'TIMED',
        ]);

        $response = $this->getJson('/api/games');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'score_home',
                        'score_away',
                        'home_team',
                        'away_team',
                    ],
                ],
            ]);
    }

    public function test_games_show_returns_404_for_missing(): void
    {
        $this->getJson('/api/games/99999')->assertNotFound();
    }

    public function test_teams_show_returns_team(): void
    {
        $team = Team::factory()->create([
            'name' => 'Test FC',
            'short_name' => 'TFC',
        ]);

        $this->getJson("/api/teams/{$team->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Test FC');
            // ->assertJsonPath('name', 'Test FC');
    }
}