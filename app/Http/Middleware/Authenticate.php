<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if($request->expectsJson()) {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'error' => 'Unauthorized.'
            ], Response::HTTP_UNAUTHORIZED);
        };
    }
}
