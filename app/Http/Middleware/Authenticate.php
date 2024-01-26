<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        parent::__construct($auth);
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guard = $guards[0];

        $token = Auth::guard($guard)->getToken();
        if ($token != null) {
            $data = explode(".", $token);
            $data = base64_decode($data[1]);
            $now = time();
            $data = json_decode($data);

            if ($data->exp < $now - 60) {
                return Helper::getResponse(['status' => 401, 'message' => 'Token Expired']);
            }
        }


        if ($this->auth->guard($guard)->guest()) {
            return Helper::getResponse(['status' => 401, 'message' => 'Unauthorised']);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
