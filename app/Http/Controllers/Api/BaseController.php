<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Send a successful response.
     *
     * @param  mixed  $result  The result data.
     * @param  string  $message  The success message.
     * @param  int  $code  The HTTP status code.
     * @return JsonResponse The JSON response.
     */
    public function sendResponse($result, $message, $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $result,
        ], $code);
    }

    /**
     * Send an error response.
     *
     * @param  string  $error  The error message.
     * @param  array  $errorMessages  Additional error messages.
     * @param  int  $code  The HTTP status code.
     * @return JsonResponse The JSON response.
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
