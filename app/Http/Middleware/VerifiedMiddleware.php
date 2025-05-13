<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'You need to login to access this resource.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user->last_seen_at = now();
        $user->update();
        if ($user->hasVerifiedEmail() || $user->hasVerifiedPhone()) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You need to verify your ID to access this resource.',
            'status' => false,
            'code' => Response::HTTP_FORBIDDEN,
        ]);
    }
}
