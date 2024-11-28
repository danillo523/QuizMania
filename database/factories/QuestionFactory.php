<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'text' => $this->faker->sentence,
            'correct_answer' => $this->faker->boolean,
        ];
    }
}
