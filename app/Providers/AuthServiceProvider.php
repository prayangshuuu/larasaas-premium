<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Setting;
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
        // \App\Models\SomeModel::class => \App\Policies\SomeModelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // If you add entries to $policies, this will register them (harmless if empty).
        if (method_exists($this, 'registerPolicies')) {
            $this->registerPolicies();
        }

        /**
         * Gate used anywhere (views, policies, controllers) to guard the admin area.
         * Your User model has isAdmin(), so we defer to that.
         */
        Gate::define('access-admin', fn (User $user) => $user->isAdmin());

        /**
         * Optional helper gate for impersonation. Middleware already enforces this,
         * but having a Gate makes it easy to @can('impersonate') in views.
         */
        Gate::define('impersonate', function (User $user) {
            return $user->isAdmin() && Setting::bool('features.impersonation', false);
        });

        // If you ever want a superuser override, uncomment:
        // Gate::before(fn (User $user, string $ability) => $user->isAdmin() ? true : null);
    }
}
