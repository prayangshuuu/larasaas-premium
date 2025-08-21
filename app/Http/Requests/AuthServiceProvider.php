<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Model::class => Policy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate used by routes: middleware('can:access-admin')
        Gate::define('access-admin', function ($user) {
            // Works if you use either a boolean `is_admin` or a string `role = 'admin'`
            return (bool)($user->is_admin ?? false) || (($user->role ?? null) === 'admin');
        });
    }
}
