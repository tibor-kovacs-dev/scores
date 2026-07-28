<?php

namespace Tests\Feature;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_team_by_name(): void
    {
        Team::factory()->create(['name' => 'Corinthians', 'short_name' => 'COR']);

        $response = $this->getJson('/api/search?q=cor');

        $response->assertOk()
            ->assertJsonCount(1, 'teams');
    }

    public function test_search_requires_min_2_chars(): void
    {
        $this->getJson('/api/search?q=a')
            ->assertOk()
            ->assertJson([
                'teams' => [],
                'games' => [],
            ]);
    }
}