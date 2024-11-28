<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leaderboard>
 */
class LeaderboardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Leaderboard::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'score' => $this->faker->numberBetween(0, 100),
        ];
    }
}
