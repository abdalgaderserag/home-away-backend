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
        if ($user->can('verified')) {
            return $next($request);
        }
        
        return response()->json([
            'message' => 'You need to verify your ID to access this resource.',
            'status' => false,
            'code' => Response::HTTP_FORBIDDEN,
        ]);
    }
}
