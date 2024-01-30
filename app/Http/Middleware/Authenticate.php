<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Exception;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return Helper::getResponse(['status' => 400, 'message' => 'Token not provided']);
        }

        if (Auth::guard($guards[0])->check()) {
            return $next($request);
        }

        return Helper::getResponse(['status' => 401, 'message' => 'Unauthorised']);
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
