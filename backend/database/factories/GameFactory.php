<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_id' => fake()->unique()->numberBetween(100000, 999999),
            'competition_code' => 'PL',
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'score_home' => 0,
            'score_away' => 0,
            'status' => 'TIMED',
            'utc_date' => now()->addHours(2),
            'minute' => 0,
        ];
    }
}
