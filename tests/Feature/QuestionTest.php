<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Quiz;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    public function it_retrieves_questions_for_a_quiz(): void
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(3)->create(['quiz_id' => $quiz->id]);

        $response = $this->getJson(route('api.quizzes.questions.index', $quiz->id));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $questions[0]->id])
            ->assertJsonFragment(['id' => $questions[1]->id])
            ->assertJsonFragment(['id' => $questions[2]->id]);
    }

    public function it_returns_404_if_quiz_not_found_when_retrieving_questions(): void
    {
        $response = $this->getJson(route('api.quizzes.questions.index', 'non-existing-id'));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }

    public function it_retrieves_a_question_for_a_quiz(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);

        $response = $this->getJson(route('api.quizzes.questions.show', [$quiz->id, $question->id]));

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $question->id]);
    }

    public function it_returns_404_if_quiz_not_found_when_retrieving_a_question(): void
    {
        $response = $this->getJson(route('api.quizzes.questions.show', ['non-existing-id', 'non-existing-id']));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }

    public function it_creates_a_question_for_a_quiz()
    {
        $quiz = Quiz::factory()->create();
        $data = [
            'text' => 'Sample question?',
            'correct_answer' => 'true',
        ];

        $response = $this->postJson(route('api.quizzes.questions.store', $quiz->id), $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['text' => 'Sample question?']);
    }

    public function it_returns_404_if_quiz_not_found_when_creating_a_question(): void
    {
        $data = [
            'text' => 'Sample question?',
            'correct_answer' => 'true',
        ];

        $response = $this->postJson(route('api.quizzes.questions.store', 'non-existing-id'), $data);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }

    public function it_updates_a_question_for_a_quiz(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
        $data = [
            'text' => 'Updated question?',
            'correct_answer' => 'false',
        ];

        $response = $this->putJson(route('api.quizzes.questions.update', [$quiz->id, $question->id]), $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['text' => 'Updated question?']);
    }

    public function it_returns_404_if_quiz_not_found_when_updating_a_question(): void
    {
        $data = [
            'text' => 'Updated question?',
            'correct_answer' => 'false',
        ];

        $response = $this->putJson(
            route('api.quizzes.questions.update', ['non-existing-id', 'non-existing-id']),
            $data
        );

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }

    public function it_deletes_a_question_for_a_quiz(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);

        $response = $this->deleteJson(route('api.quizzes.questions.destroy', [$quiz->id, $question->id]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Question deleted successfully.']);
    }

    public function it_returns_404_if_quiz_not_found_when_deleting_a_question(): void
    {
        $response = $this->deleteJson(route('api.quizzes.questions.destroy', ['non-existing-id', 'non-existing-id']));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Quiz not found']);
    }
}
