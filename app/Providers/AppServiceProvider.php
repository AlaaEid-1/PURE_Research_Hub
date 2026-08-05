<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Models\Research;
use App\Observers\ResearchObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Research::observe(ResearchObserver::class);
    }
}
