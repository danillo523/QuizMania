<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuestionController extends BaseController
{
    public function index(string $quizId): JsonResponse
    {
        $quiz = Quiz::find($quizId);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        return $this->sendResponse($quiz->questions, 'Questions retrieved successfully.', 200);
    }

    public function show(string $quizId, string $questionId): JsonResponse
    {
        $quiz = Quiz::find($quizId);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        $question = Question::find($questionId);

        if (! $question || $question->quiz_id !== $quiz->id) {
            return $this->sendError('Question not found or does not belong to this quiz', [], 404);
        }

        return $this->sendResponse($question, 'Question retrieved successfully.', 200);
    }

    public function store(Request $request, string $quizId): JsonResponse
    {
        $quiz = Quiz::find($quizId);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'text' => 'required|string',
            'correct_answer' => 'required|in:true,false,1,0,True,False',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $data = $validator->validated();

        $data['correct_answer'] = filter_var($data['correct_answer'], FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('questions', 'public');
        }

        $question = $quiz->questions()->create($data);

        return $this->sendResponse($question, 'Question created successfully.', 201);
    }

    public function update(Request $request, string $quizId, string $questionId): JsonResponse
    {
        $quiz = Quiz::find($quizId);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        $question = Question::find($questionId);

        if (! $question || $question->quiz_id !== $quiz->id) {
            return $this->sendError('Question not found or does not belong to this quiz', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'text' => 'sometimes|required|string',
            'correct_answer' => 'required|in:true,false,1,0,True,False',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $data = $validator->validated();

        $data['correct_answer'] = filter_var($data['correct_answer'], FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('image') && $quiz->image) {
            Storage::disk('public')->delete($quiz->image);
        }
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('questions', 'public');
        }

        $question->update($data);

        return $this->sendResponse($question->fresh(), 'Question updated successfully.', 200);
    }

    public function destroy(string $quizId, string $questionId): JsonResponse
    {
        $quiz = Quiz::find($quizId);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        $question = Question::find($questionId);

        if (! $question || $question->quiz_id !== $quiz->id) {
            return $this->sendError('Question not found or does not belong to this quiz', [], 404);
        }

        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        $question->delete();

        return $this->sendResponse([], 'Question deleted successfully.', 200);
    }
}
