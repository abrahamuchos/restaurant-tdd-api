<?php

namespace App\Providers;

use App\Enums\Roles;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Grant "Super Admin" role all permissions without defining it on database
        Gate::before(function ($user, $ability) {
            return $user->hasRole(Roles::ADMIN) ? true : null;
        });
    }
}
