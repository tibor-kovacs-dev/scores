<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_id' => fake()->unique()->numberBetween(1000, 99999),
            'name' => fake()->company().' FC',
            'short_name' => strtoupper(fake()->lexify('???')),
            'tla' => strtoupper(fake()->lexify('???')),
            'logo_url' => 'https://example.com/logo.png',
        ];
    }
}
