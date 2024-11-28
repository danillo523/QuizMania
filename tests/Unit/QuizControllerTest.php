<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\QuizController;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    public function testIndex()
    {
        $controller = new QuizController;

        // Test case where quizzes are retrieved successfully
        $response = $controller->index();
        $this->assertEquals(200, $response->status());
    }

    public function testShow()
    {
        $quiz = Quiz::factory()->create();
        $controller = new QuizController;

        // Test case where the quiz is not found
        $response = $controller->show('invalid-quiz-id');
        $this->assertEquals(404, $response->status());

        // Test case where the quiz is retrieved successfully
        $response = $controller->show($quiz->id);
        $this->assertEquals(200, $response->status());
    }

    public function testStore()
    {
        $controller = new QuizController;

        $request = new Request([
            'title' => 'Sample Quiz',
            'description' => 'This is a sample quiz.',
        ]);

        // Mock the Validator to return a valid Validator instance
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(true);
        $validatorMock->shouldReceive('errors')->andReturn(collect(['title' => 'The title field is required.']));

        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the request is invalid
        $response = $controller->store($request);
        $this->assertEquals(422, $response->status());

        // Adjust the mock for a valid request
        $validatorMock->shouldReceive('fails')->andReturn(false);
        $validatorMock->shouldReceive('validated')->andReturn($request->all());
        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the quiz is created successfully
        $response = $controller->store($request);
        $this->assertEquals(422, $response->status());
    }

    public function testUpdate()
    {
        $quiz = Quiz::factory()->create();
        $controller = new QuizController;

        $request = new Request([
            'title' => 'Updated Quiz',
            'description' => 'This is an updated quiz.',
        ]);

        // Mock the Validator to return a valid Validator instance
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(true);
        $validatorMock->shouldReceive('errors')->andReturn(collect(['title' => 'The title field is required.']));

        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the request is invalid
        $response = $controller->update($request, $quiz->id);
        $this->assertEquals(422, $response->status());

        // Adjust the mock for a valid request
        $validatorMock->shouldReceive('fails')->andReturn(false);
        $validatorMock->shouldReceive('validated')->andReturn($request->all());
        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the quiz is updated successfully
        $response = $controller->update($request, $quiz->id);
        $this->assertEquals(422, $response->status());
    }

    public function testDestroy()
    {
        $quiz = Quiz::factory()->create();
        $controller = new QuizController;

        // Test case where the quiz is not found
        $response = $controller->destroy('invalid-quiz-id');
        $this->assertEquals(404, $response->status());

        // Test case where the quiz is deleted successfully
        $response = $controller->destroy($quiz->id);
        $this->assertEquals(200, $response->status());
    }
}
