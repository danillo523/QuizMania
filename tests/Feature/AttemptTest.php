<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Tests\TestCase;

class AttemptTest extends TestCase
{
    public function it_starts_a_new_quiz_attempt(): void
    {
        $quiz = Quiz::factory()->create();
        $this->actingAs(User::factory()->create());

        $response = $this->postJson(route('attempts.start', $quiz->id));

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'quiz_id',
                'started_at',
                'quiz' => [
                    'questions' => [
                        '*' => [
                            'id',
                            'qtext',
                            'correct_answer',
                        ],
                    ],
                ],
            ],
            'message',
        ]);
    }

    public function it_fails_to_start_a_new_attempt_if_one_already_exists(): void
    {
        $quiz = Quiz::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        Attempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'finished_at' => null,
        ]);

        $response = $this->postJson(route('attempts.start', $quiz->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'You already have an ongoing attempt for this quiz',
        ]);
    }

    public function it_fails_to_start_a_new_attempt_if_quiz_is_already_completed(): void
    {
        $quiz = Quiz::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        Attempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'finished_at' => now(),
        ]);

        $response = $this->postJson(route('attempts.start', $quiz->id));

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'You have already completed this quiz',
        ]);
    }

    public function it_records_an_answer_correctly(): void
    {
        $attempt = Attempt::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $attempt->quiz_id]);
        $this->actingAs($attempt->user);

        $response = $this->postJson(route('attempts.answer'), [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer' => $question->correct_answer,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Answer recorded successfully',
            'data' => [
                'is_correct' => true,
                'points_earned' => 10,
                'current_score' => 10,
                'questions_answered' => 1,
                'total_questions' => 1,
                'is_completed' => true,
            ],
        ]);
    }

    public function it_fails_to_record_an_answer_if_attempt_does_not_belong_to_user(): void
    {
        $attempt = Attempt::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $attempt->quiz_id]);
        $this->actingAs(User::factory()->create());

        $response = $this->postJson(route('attempts.answer'), [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer' => $question->correct_answer,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This attempt does not belong to you',
        ]);
    }

    public function it_fails_to_record_an_answer_if_question_does_not_belong_to_quiz(): void
    {
        $attempt = Attempt::factory()->create();
        $question = Question::factory()->create();
        $this->actingAs($attempt->user);

        $response = $this->postJson(route('attempts.answer'), [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer' => $question->correct_answer,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This question does not belong to this quiz',
        ]);
    }

    public function it_fails_to_record_an_answer_if_attempt_is_finished(): void
    {
        $attempt = Attempt::factory()->create(['finished_at' => now()]);
        $question = Question::factory()->create(['quiz_id' => $attempt->quiz_id]);
        $this->actingAs($attempt->user);

        $response = $this->postJson(route('attempts.answer'), [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer' => $question->correct_answer,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This quiz attempt has already been finished',
        ]);
    }

    public function it_fails_to_record_an_answer_if_question_already_answered(): void
    {
        $attempt = Attempt::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $attempt->quiz_id]);
        $this->actingAs($attempt->user);

        $attempt->answers()->create([
            'question_id' => $question->id,
            'user_answer' => $question->correct_answer,
            'is_correct' => true,
        ]);

        $response = $this->postJson(route('attempts.answer'), [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer' => $question->correct_answer,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This question has already been answered',
        ]);
    }
}
