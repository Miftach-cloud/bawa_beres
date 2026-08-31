<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
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
        // Superadmin bypass for Owner
        Gate::before(function (User $user, string $ability) {
            if ($user->isOwner()) {
                return true;
            }
        });

        // Gates definition
        Gate::define('access-admin', function (User $user) {
            return in_array($user->role, [UserRole::OWNER, UserRole::ADMIN, UserRole::OPERATION], true);
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->isOwner();
        });

        Gate::define('manage-orders', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
        });

        Gate::define('manage-customers', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
        });

        Gate::define('manage-services', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
        });

        Gate::define('manage-quotations', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
        });

        Gate::define('manage-payments', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::ADMIN]);
        });

        Gate::define('manage-schedule', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::ADMIN, UserRole::OPERATION]);
        });

        Gate::define('manage-inventory', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::OPERATION]);
        });

        Gate::define('manage-storage', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::OPERATION]);
        });

        Gate::define('manage-documentation', function (User $user) {
            return $user->hasRole([UserRole::OWNER, UserRole::OPERATION]);
        });
    }
}
