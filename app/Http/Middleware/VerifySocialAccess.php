<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySocialAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Already authenticated'], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
