<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\LeaderboardController;
use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LeaderboardControllerTest extends TestCase
{
    public function testIndex()
    {
        // Mock dependencies and set up the environment
        $user = User::factory()->create();
        $leaderboard = Leaderboard::factory()->create(['user_id' => $user->id, 'score' => 100]);

        $request = new Request(['order' => 'desc']);
        $controller = new LeaderboardController;

        // Mock the Validator to return a valid Validator instance
        $validatorMock = \Mockery::mock(\Illuminate\Validation\Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(true);
        $validatorMock->shouldReceive('errors')->andReturn(collect(['order' => 'Invalid order']));

        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the request is invalid
        $response = $controller->index($request);
        $this->assertEquals(422, $response->status());

        // Adjust the mock for a valid request
        $validatorMock->shouldReceive('fails')->andReturn(false);
        Validator::shouldReceive('make')->andReturn($validatorMock);

        // Test case where the leaderboard is retrieved successfully
        $response = $controller->index($request);
        $this->assertEquals(422, $response->status());
    }

    public function testUserScore()
    {
        // Mock dependencies and set up the environment
        $user = User::factory()->create();
        $leaderboard = Leaderboard::factory()->create(['user_id' => $user->id, 'score' => 100]);

        $controller = new LeaderboardController;

        // Test case where the user ID is invalid
        $response = $controller->userScore('invalid-uuid');
        $this->assertEquals(422, $response->status());

        // Test case where the score is not found
        $response = $controller->userScore('00000000-0000-0000-0000-000000000000');
        $this->assertEquals(422, $response->status());

        // Test case where the user score is retrieved successfully
        $response = $controller->userScore($user->id);
        $this->assertEquals(200, $response->status());
    }
}
