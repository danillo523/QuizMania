<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Access denied. You do not have permission to access this area.',
            ], 403);
        }

        return $next($request);
    }
}
