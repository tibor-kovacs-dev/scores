<?php

namespace Tests\Feature;

use App\Services\FootballDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class StandingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_standings_returns_data_for_competition(): void
    {
        $fakeStandings = [
            'standings' => [
                [
                    'group' => 'GROUP_A',
                    'table' => [
                        [
                            'playedGames' => 10,
                            'won' => 6,
                            'draw' => 2,
                            'lost' => 2,
                            'goalsFor' => 18,
                            'goalsAgainst' => 8,
                            'goalDifference' => 10,
                            'points' => 20,
                            'team' => [
                                'id' => 1,
                                'name' => 'Test United',
                                'crest' => 'https://example.com/crest.png',
                                'shortName' => 'TST',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mock = Mockery::mock(FootballDataService::class);
        $mock->shouldReceive('getStandings')
            ->once()
            ->with('PL')
            ->andReturn($fakeStandings);

        $this->app->instance(FootballDataService::class, $mock);

        $response = $this->getJson('/api/standings?competition=PL');

        $response->assertOk()
            ->assertJsonPath('standings.0.table.0.team.name', 'Test United');
    }

    public function test_standings_returns_error_when_service_fails(): void
    {
        $mock = Mockery::mock(FootballDataService::class);
        $mock->shouldReceive('getStandings')
            ->once()
            ->andReturn(null);

        $this->app->instance(FootballDataService::class, $mock);

        $this->getJson('/api/standings?competition=PL')
            ->assertStatus(500);
    }

    public function test_top_scorers_returns_data(): void
    {
        $fakeScorers = [
            'scorers' => [
                [
                    'player' => ['id' => 1, 'name' => 'Test Player'],
                    'team' => ['id' => 10, 'name' => 'Test FC'],
                    'goals' => 12,
                ],
            ],
        ];

        $mock = Mockery::mock(FootballDataService::class);
        $mock->shouldReceive('getTopScorers')
            ->once()
            ->with('PL')
            ->andReturn($fakeScorers);

        $this->app->instance(FootballDataService::class, $mock);

        $this->getJson('/api/top-scorers?competition=PL')
            ->assertOk()
            ->assertJsonPath('scorers.0.player.name', 'Test Player')
            ->assertJsonPath('scorers.0.goals', 12);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}