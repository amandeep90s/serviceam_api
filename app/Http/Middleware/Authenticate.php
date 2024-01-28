<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Exception;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            // Attempt to verify the token and get the user
            $user = JWTAuth::parseToken()->authenticate();

            // Check if the token is expired
            if (!$user) {
                return Helper::getResponse(['status' => 401, 'message' => 'Token Expired']);
            }

            // Your other authentication logic goes here

        } catch (Exception $e) {
            return Helper::getResponse(['status' => 401, 'message' => 'Unauthorised']);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->is('api/*') || !$request->expectsJson()
            ? null
            : route('login');
    }
}
