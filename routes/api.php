<?php

use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::get('me', 'me')->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum', 'check.role:admin'])->group(function () {
    Route::post('quizzes', [QuizController::class, 'store']);
    Route::put('quizzes/{quiz}', [QuizController::class, 'update']);
    Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy']);

    Route::post('quizzes/{quiz}/questions', [QuestionController::class, 'store']);
    Route::put('quizzes/{quiz}/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('quizzes/{quiz}/questions/{question}', [QuestionController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('quizzes', QuizController::class)->except(['store', 'update', 'destroy']);
    Route::apiResource('quizzes.questions', QuestionController::class)->except(['store', 'update', 'destroy']);

    Route::prefix('quizzes/{quiz}')->group(function () {
        Route::post('start', [AttemptController::class, 'start']);
    });
    Route::prefix('attempts')->group(function () {
        Route::post('answer', [AttemptController::class, 'answer']);
        Route::post('finish', [AttemptController::class, 'finish']);
        Route::get('{attempt}', [AttemptController::class, 'show']);
        Route::get('/', [AttemptController::class, 'userAttempts']);
    });

    Route::prefix('leaderboard')->group(function () {
        Route::get('/', [LeaderboardController::class, 'index']);
        Route::get('{order}', [LeaderboardController::class, 'index']);
        Route::get('user/{user}', [LeaderboardController::class, 'userScore']);
    });
});
