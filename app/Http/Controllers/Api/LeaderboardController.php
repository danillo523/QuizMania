<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaderboardController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order' => 'in:asc,desc',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $order = $request->query('order', 'desc'); // Default to 'desc' if not provided

        $leaderboard = Leaderboard::with('user')
            ->orderBy('score', $order)
            ->get()
            ->map(function ($entry) {
                $user = $entry->user;
                $email = $this->anonymizeEmail($user->email);

                return [
                    'name' => $user->name,
                    'score' => $entry->score,
                    'email' => $email,
                ];
            });

        return $this->sendResponse($leaderboard, 'Leaderboard of all users.');
    }

    public function userScore(string $userId): JsonResponse
    {
        $validator = Validator::make(['user_id' => $userId], ['user_id' => 'uuid|exists:users,id']);
        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $user = User::find($userId);

        $score = Leaderboard::where('user_id', $userId)->first();

        if (! $score) {
            return $this->sendError('Score not found', [], 404);
        }

        return $this->sendResponse([
            'name' => $user->name,
            'score' => $score->score,
            'email' => $this->anonymizeEmail($user->email),
        ], 'Your total score.');
    }

    private function anonymizeEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $local = substr($local, 0, 2).str_repeat('*', strlen($local) - 2);
        $domain = substr($domain, 0, 2).str_repeat('*', strlen($domain) - 2);

        return "$local@$domain";
    }
}
