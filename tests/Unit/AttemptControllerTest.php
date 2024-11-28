<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\AttemptController;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AttemptControllerTest extends TestCase
{
    public function testStart(): void
    {
        $quiz = Quiz::factory()->create();
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $existingAttempt = Attempt::factory()->create(['user_id' => $user->id, 'quiz_id' => $quiz->id]);
        $controller = new AttemptController;
        $response = $controller->start($quiz);
        $this->assertEquals(200, $response->status());

        $completedAttempt = Attempt::factory()->create(['user_id' => $user->id, 'quiz_id' => $quiz->id, 'finished_at' => now()]);
        $response = $controller->start($quiz);
        $this->assertEquals(200, $response->status());

        $response = $controller->start($quiz);
        $this->assertEquals(200, $response->status());
    }

    public function testAnswer(): void
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
        $attempt = Attempt::factory()->create(['quiz_id' => $quiz->id]);
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $request = new Request([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer' => true,
        ]);

        // Mock the Validator to return a valid Validator instance
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(false);
        $validatorMock->shouldReceive('validated')->andReturn($request->all());

        Validator::shouldReceive('make')->andReturn($validatorMock);

        $controller = new AttemptController;
        $response = $controller->answer($request);
        $this->assertEquals(403, $response->status());
    }

    public function testShow(): void
    {
        $attempt = Attempt::factory()->create();
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $controller = new AttemptController;
        $response = $controller->show($attempt);
        $this->assertEquals(403, $response->status());

        $attempt->user_id = $user->id;
        $response = $controller->show($attempt);
        $this->assertEquals(200, $response->status());
    }

    public function testUserAttempts(): void
    {
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        $controller = new AttemptController;
        $response = $controller->userAttempts();
        $this->assertEquals(200, $response->status());
    }
}
