<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Frame>
 */
class FrameFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => (string) Str::ulid(),
            'game_id' => null, // set in test/seed explicitly
            'home_player' => null,
            'away_player' => null,
            'game_no' => $this->faker->numberBetween(1, 12),
            'home_score' => $this->faker->numberBetween(0, 10),
            'away_score' => $this->faker->numberBetween(0, 10),
            'eight_ball_clear_home' => $this->faker->boolean(20),
            'eight_ball_clear_away' => $this->faker->boolean(20),
            'home_game_no' => $this->faker->numberBetween(0, 5),
            'away_game_no' => $this->faker->numberBetween(0, 5),
        ];
    }
}
