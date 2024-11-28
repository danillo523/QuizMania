<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuizController extends BaseController
{
    public function index(): JsonResponse
    {
        $quizzes = Quiz::with('questions')->get();

        return $this->sendResponse($quizzes, 'Quizzes listed successfully.', 200);
    }

    public function show(string $id): JsonResponse
    {
        $quiz = Quiz::with('questions')->find($id);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        return $this->sendResponse($quiz, 'Quiz listed successfully.', 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('quizzes', 'public');
        }

        $quiz = Quiz::create($data);

        return $this->sendResponse($quiz, 'Quiz created successfully.', 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $quiz = Quiz::find($id);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('image') && $quiz->image) {
            Storage::disk('public')->delete($quiz->image);
        }
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('quizzes', 'public');
        }

        $quiz->update($data);

        return $this->sendResponse($quiz->fresh(), 'Quiz updated successfully.', 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $quiz = Quiz::find($id);

        if (! $quiz) {
            return $this->sendError('Quiz not found', [], 404);
        }

        if ($quiz->image) {
            Storage::disk('public')->delete($quiz->image);
        }

        $quiz->delete();

        return $this->sendResponse([], 'Quiz deleted successfully.', 200);
    }
}
