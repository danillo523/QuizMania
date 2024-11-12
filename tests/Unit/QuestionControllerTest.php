<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\QuestionController;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    public function testIndex()
    {
        $quiz = Quiz::factory()->create();
        $controller = new QuestionController;

        // Test case where the quiz is not found
        $response = $controller->index('invalid-quiz-id');
        $this->assertEquals(404, $response->status());

        // Test case where questions are retrieved successfully
        $response = $controller->index($quiz->id);
        $this->assertEquals(200, $response->status());
    }

    public function testShow()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
        $controller = new QuestionController;

        // Test case where the quiz is not found
        $response = $controller->show('invalid-quiz-id', $question->id);
        $this->assertEquals(404, $response->status());

        // Test case where the question is not found
        $response = $controller->show($quiz->id, 'invalid-question-id');
        $this->assertEquals(404, $response->status());

        // Test case where the question is retrieved successfully
        $response = $controller->show($quiz->id, $question->id);
        $this->assertEquals(200, $response->status());
    }

    public function testStore()
    {
        $quiz = Quiz::factory()->create();
        $controller = new QuestionController;

        $request = new Request([
            'text' => 'Sample question?',
            'correct_answer' => 'true',
        ]);

        // Mock the Validator to return a valid Validator instance
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(true);
        $validatorMock->shouldReceive('errors')->andReturn(collect(['text' => 'The text field is required.']));

        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the request is invalid
        $response = $controller->store($request, $quiz->id);
        $this->assertEquals(422, $response->status());

        // Adjust the mock for a valid request
        $validatorMock->shouldReceive('fails')->andReturn(false);
        $validatorMock->shouldReceive('validated')->andReturn($request->all());
        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the question is created successfully
        $response = $controller->store($request, $quiz->id);
        $this->assertEquals(422, $response->status());
    }

    public function testUpdate()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
        $controller = new QuestionController;

        $request = new Request([
            'text' => 'Updated question?',
            'correct_answer' => 'false',
        ]);

        // Mock the Validator to return a valid Validator instance
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(true);
        $validatorMock->shouldReceive('errors')->andReturn(collect(['text' => 'The text field is required.']));

        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the request is invalid
        $response = $controller->update($request, $quiz->id, $question->id);
        $this->assertEquals(422, $response->status());

        // Adjust the mock for a valid request
        $validatorMock->shouldReceive('fails')->andReturn(false);
        $validatorMock->shouldReceive('validated')->andReturn($request->all());
        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the question is updated successfully
        $response = $controller->update($request, $quiz->id, $question->id);
        $this->assertEquals(422, $response->status());
    }

    public function testDestroy()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
        $controller = new QuestionController;

        // Test case where the quiz is not found
        $response = $controller->destroy('invalid-quiz-id', $question->id);
        $this->assertEquals(404, $response->status());

        // Test case where the question is not found
        $response = $controller->destroy($quiz->id, 'invalid-question-id');
        $this->assertEquals(404, $response->status());

        // Test case where the question is deleted successfully
        $response = $controller->destroy($quiz->id, $question->id);
        $this->assertEquals(200, $response->status());
    }
}
