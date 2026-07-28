<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Team;
use App\Services\FootballDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FootballDataServiceCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleanup_deletes_old_finished_matches(): void
    {
        $home = Team::factory()->create();
        $away = Team::factory()->create();

        Game::factory()->create([
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'status' => 'FINISHED',
            'utc_date' => now()->subDays(40),
        ]);

        Game::factory()->create([
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'status' => 'FINISHED',
            'utc_date' => now()->subDays(2),
        ]);

        $deleted = app(FootballDataService::class)->cleanupOldMatches(30);

        $this->assertEquals(1, $deleted);
        $this->assertEquals(1, Game::count());
    }
}