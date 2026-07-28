<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Team;
use App\Services\FootballDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FootballDataServiceTest extends TestCase
{
    use RefreshDatabase;

    private FootballDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FootballDataService::class);
    }

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

        $deleted = $this->service->cleanupOldMatches(30);

        $this->assertEquals(1, $deleted);
        $this->assertEquals(1, Game::count());
    }

    public function test_cleanup_does_not_delete_live_matches(): void
    {
        $home = Team::factory()->create();
        $away = Team::factory()->create();

        Game::factory()->create([
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'status' => 'IN_PLAY',
            'utc_date' => now()->subDays(40),
        ]);

        $deleted = $this->service->cleanupOldMatches(30);

        $this->assertEquals(0, $deleted);
        $this->assertEquals(1, Game::count());
    }

    public function test_clear_caches_removes_competition_cache(): void
    {
        Cache::put('available_competitions', ['PL', 'BSA'], 3600);
        Cache::put('teams_PL', [['id' => 1]], 3600);
        Cache::put('standings_PL_'.date('Y'), ['standings' => []], 3600);

        Cache::put('available_competitions', ['PL'], 3600);

        $this->service->clearCaches();

        $this->assertFalse(Cache::has('teams_PL'));
        $this->assertFalse(Cache::has('standings_PL_'.date('Y')));
    }
}