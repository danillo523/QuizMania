<?php

namespace Tests\Feature;

use App\Models\Leaderboard;
use App\Models\User;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    public function it_returns_leaderboard_in_descending_order_by_default(): void
    {
        $response = $this->getJson('/api/leaderboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['name', 'score', 'email'],
                ],
                'message',
            ])
            ->assertJsonFragment(['message' => 'Leaderboard of all users.']);
    }

    public function it_returns_leaderboard_in_ascending_order_when_specified(): void
    {
        $response = $this->getJson('/api/leaderboard/asc');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['name', 'score', 'email'],
                ],
                'message',
            ])
            ->assertJsonFragment(['message' => 'Leaderboard of all users.']);
    }

    public function it_returns_error_for_invalid_order_parameter(): void
    {
        $response = $this->getJson('/api/leaderboard/ord');

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['order'],
            ])
            ->assertJsonFragment(['message' => 'Request with errors, please fix these errors below']);
    }

    public function it_returns_user_score_for_valid_user_id(): void
    {
        $user = User::factory()->create();
        Leaderboard::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/leaderboard/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['name', 'score', 'email'],
                'message',
            ])
            ->assertJsonFragment(['message' => 'Your total score.']);
    }

    public function it_returns_error_for_invalid_user_id(): void
    {
        $response = $this->getJson('/api/leaderboard/invalid-uuid');

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['user_id'],
            ])
            ->assertJsonFragment(['message' => 'Request with errors, please fix these errors below']);
    }

    public function it_returns_error_when_user_score_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/leaderboard/{$user->id}");

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJsonFragment(['message' => 'Score not found']);
    }
}
