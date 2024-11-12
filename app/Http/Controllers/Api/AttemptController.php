<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Attempt;
use App\Models\Leaderboard;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttemptController extends BaseController
{
    private const POINTS_PER_CORRECT_ANSWER = 10;

    public function start(Quiz $quiz): JsonResponse
    {
        $existingAttempt = Attempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNull('finished_at')
            ->first();

        if ($existingAttempt) {
            return $this->sendResponse(
                $existingAttempt->load('quiz.questions'),
                'You already have an ongoing attempt for this quiz'
            );
        }

        $completedAttempt = Attempt::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('finished_at')
            ->exists();

        if ($completedAttempt) {
            return $this->sendError('You have already completed this quiz', [], 403);
        }

        $attempt = Attempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'started_at' => now(),
        ]);

        return $this->sendResponse($attempt->load('quiz.questions'), 'Quiz attempt started, good luck!', 201);
    }

    public function answer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'attempt_id' => 'required|uuid|exists:attempts,id',
            'question_id' => 'required|uuid|exists:questions,id',
            'answer' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $data = $validator->validated();

        $attempt = Attempt::findOrFail($request->attempt_id);
        $question = Question::findOrFail($request->question_id);

        if ($attempt->user_id !== auth()->id()) {
            return $this->sendError('This attempt does not belong to you', [], 403);
        }

        if ($question->quiz_id !== $attempt->quiz_id) {
            return $this->sendError('This question does not belong to this quiz', [], 403);
        }

        if ($attempt->finished_at) {
            return $this->sendError('This quiz attempt has already been finished', [], 403);
        }

        if ($attempt->answers()->where('question_id', $question->id)->exists()) {
            return $this->sendError('This question has already been answered', [], 403);
        }

        $isCorrect = $data['answer'] == $question->correct_answer;

        $answer = $attempt->answers()->create([
            'question_id' => $question->id,
            'user_answer' => $data['answer'],
            'is_correct' => $isCorrect,
        ]);

        if (! $answer) {
            return $this->sendError('Failed to record answer', [], 500);
        }

        if ($isCorrect) {
            $attempt->increment('score', self::POINTS_PER_CORRECT_ANSWER);
        }

        $totalQuestions = $attempt->quiz->questions->count();
        $answeredQuestions = $attempt->answers->count();
        $isCompleted = $answeredQuestions === $totalQuestions;

        if ($isCompleted) {
            $attempt->update(['finished_at' => now()]);

            $leaderboard = Leaderboard::firstOrCreate(['user_id' => auth()->id()]);
            $leaderboard->increment('score', $attempt->score);

            $message = 'Quiz completed successfully. Your score is '.$attempt->score;
        } else {
            $message = 'Answer recorded successfully';
        }

        return $this->sendResponse([
            'is_correct' => $isCorrect,
            'points_earned' => $isCorrect ? self::POINTS_PER_CORRECT_ANSWER : 0,
            'current_score' => $attempt->score,
            'questions_answered' => $answeredQuestions,
            'total_questions' => $totalQuestions,
            'is_completed' => $isCompleted,
            'attempt' => $attempt->fresh(['answers', 'quiz']),
        ], $message);
    }

    public function show(Attempt $attempt): JsonResponse
    {
        if ($attempt->user_id !== auth()->id()) {
            return $this->sendError('This attempt does not belong to you', [], 403);
        }

        return $this->sendResponse([
            'attempt' => $attempt->load(['answers', 'quiz.questions']),
            'statistics' => [
                'total_questions' => $attempt->quiz->questions->count(),
                'questions_answered' => $attempt->answers->count(),
                'correct_answers' => $attempt->answers->where('is_correct', true)->count(),
                'score' => $attempt->score,
                'is_completed' => ! is_null($attempt->finished_at),
                'started_at' => $attempt->started_at,
                'finished_at' => $attempt->finished_at,
            ],
        ], 'Attempt details', 200);
    }

    public function userAttempts(): JsonResponse
    {
        $attempts = Attempt::where('user_id', auth()->id())
            ->with(['quiz'])
            ->get()
            ->map(function ($attempt) {
                return [
                    'attempt' => $attempt,
                    'statistics' => [
                        'total_questions' => $attempt->quiz->questions->count(),
                        'questions_answered' => $attempt->answers->count(),
                        'correct_answers' => $attempt->answers->where('is_correct', true)->count(),
                        'score' => $attempt->score,
                        'is_completed' => ! is_null($attempt->finished_at),
                    ],
                ];
            });

        return $this->sendResponse($attempts, 'User attempts');
    }
}
