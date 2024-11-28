<?php

namespace Tests\Feature;

use App\Models\Quiz;
use Tests\TestCase;

class QuizTest extends TestCase
{
    public function it_lists_all_quizzes(): void
    {
        $quizzes = Quiz::factory()->count(3)->create();

        $response = $this->getJson(route('api.quizzes.index'));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $quizzes[0]->id])
            ->assertJsonFragment(['id' => $quizzes[1]->id])
            ->assertJsonFragment(['id' => $quizzes[2]->id]);
    }

    public function it_shows_a_quiz_with_questions(): void
    {
        $quiz = Quiz::factory()->hasQuestions(3)->create();

        $response = $this->getJson(route('api.quizzes.show', $quiz->id));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $quiz->id])
            ->assertJsonFragment(['id' => $quiz->questions[0]->id])
            ->assertJsonFragment(['id' => $quiz->questions[1]->id])
            ->assertJsonFragment(['id' => $quiz->questions[2]->id]);
    }

    public function it_returns_404_if_quiz_not_found_when_showing(): void
    {
        $response = $this->getJson(route('api.quizzes.show', 'non-existing-id'));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }

    public function it_creates_a_quiz(): void
    {
        $data = [
            'title' => 'Sample Quiz',
            'description' => 'This is a sample quiz description.',
        ];

        $response = $this->postJson(route('api.quizzes.store'), $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Sample Quiz']);
    }

    public function it_returns_422_if_validation_fails_when_creating_a_quiz(): void
    {
        $data = [
            'title' => '',
            'description' => '',
        ];

        $response = $this->postJson(route('api.quizzes.store'), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }

    public function it_updates_a_quiz(): void
    {
        $quiz = Quiz::factory()->create();
        $data = [
            'title' => 'Updated Quiz Title',
            'description' => 'Updated description.',
        ];

        $response = $this->putJson(route('api.quizzes.update', $quiz->id), $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Quiz Title']);
    }

    public function it_returns_404_if_quiz_not_found_when_updating(): void
    {
        $data = [
            'title' => 'Updated Quiz Title',
            'description' => 'Updated description.',
        ];

        $response = $this->putJson(route('api.quizzes.update', 'non-existing-id'), $data);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }

    public function it_deletes_a_quiz(): void
    {
        $quiz = Quiz::factory()->create();

        $response = $this->deleteJson(route('api.quizzes.destroy', $quiz->id));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Quiz deleted successfully.']);
    }

    public function it_returns_404_if_quiz_not_found_when_deleting(): void
    {
        $response = $this->deleteJson(route('api.quizzes.destroy', 'non-existing-id'));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }
}
