<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends BaseController
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised(5),
            ],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Request with errors, please fix these errors below', $validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('API')->plainTextToken;

        return $this->sendResponse(['token' => $token, 'Type' => 'Bearer'], 'Registration successful.', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails() || ! Auth::attempt($request->only('email', 'password'))) {
            return $this->sendError('Unauthorized', [], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API')->plainTextToken;

        return $this->sendResponse(['token' => $token, 'type' => 'Bearer'], 'Login successful', 200);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return $this->sendResponse([], 'Logged out successfully.', 200);
    }

    public function me(): JsonResponse
    {
        return $this->sendResponse(['user' => Auth::user()], 'Your user details.', 200);
    }
}
