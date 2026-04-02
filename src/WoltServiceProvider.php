<?php

declare(strict_types=1);

namespace Foodticket\Wolt;

use Foodticket\Wolt\Mixins\RouteMixin;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WoltServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app instanceof Application && $this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/wolt.php' => config_path('wolt.php'),
            ], 'wolt-config');
        }

        $this->mergeConfigFrom(__DIR__.'/../config/wolt.php', 'wolt');

        $this->app->singleton(WoltOauthClient::class);
        $this->app->singleton(WoltApi::class);
    }

    public function boot(): void
    {
        Route::mixin(new RouteMixin());
    }
}
