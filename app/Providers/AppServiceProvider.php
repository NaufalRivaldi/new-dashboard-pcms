<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Activity::class, ActivityPolicy::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales([
                    'en',
                    'id',
                ])
                ->visible(outsidePanels: true);
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->paginationPageOptions([10, 25, 50]);
        });
    }
}
