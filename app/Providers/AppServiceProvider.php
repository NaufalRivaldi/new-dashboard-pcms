<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\Region;
use App\Models\User;
use App\Policies\BranchPolicy;
use App\Policies\RegionPolicy;
use App\Policies\UserPolicy;
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
        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true: null;
        });

        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(Region::class, RegionPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
