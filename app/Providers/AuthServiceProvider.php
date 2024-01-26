<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Common\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            return User::where('email', $request->input('email'))->orWhere('mobile', $request->input('mobile'))->first();
        });
    }
}
