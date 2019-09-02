<?php

namespace Hanoivip\Ddd2;

use Hanoivip\Ddd2\Extensions\TokenToUserProvider;
use Hanoivip\Ddd2\Extensions\AccessTokenGuard;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Hanoivip\Ddd2\Services\DddAuthen;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/ipd.php' => config_path('ipd.php'),
            __DIR__ . '/../config/auth.php' => config_path('auth.php'),
        ]);
        $this->loadRoutesFrom(__DIR__ . '/../route/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../route/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../views', 'hanoivip');
        $this->loadTranslationsFrom( __DIR__.'/../lang', 'hanoivip');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // add guard provider: old passport
        Auth::provider('ddd2', function ($app, array $config) {
            return new TokenToUserProvider();
        });
        // add custom guard: old access_token
        Auth::extend('ddd2_token', function ($app, $name, array $config) {
            // automatically build the DI, put it as reference
            $userProvider = app(TokenToUserProvider::class);
            $request = app('request');
            return new AccessTokenGuard($userProvider, $request, $config);
        });
    }
    
    public function register()
    {
        $this->app->bind(IDddAuthen::class, DddAuthen::class);
    }
}
