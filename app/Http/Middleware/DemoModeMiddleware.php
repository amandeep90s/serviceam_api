<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Models\Common\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DemoModeMiddleware
{
    /**
     * The authentication guard factory instance.
     *
     */
    protected Auth $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = Auth::guard(strtolower(Helper::getGuard()))->user()->company_id;
        $setting = Setting::where('company_id', $companyId)->first();
        if ($setting->demo_mode == 1) {
            return Helper::getResponse(['status' => 403, 'message' => trans('admin.demomode')]);
        }
        return $next($request);
    }
}
