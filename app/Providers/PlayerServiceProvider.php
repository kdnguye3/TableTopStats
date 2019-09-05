<?php

namespace App\Providers;

use App\Services\PlayerService;
use Illuminate\Support\ServiceProvider;

class PlayerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(PlayerService::class, function ($app) {
            return new PlayerService();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
