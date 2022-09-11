<?php

namespace Hanoivip\Ddd2;

use Hanoivip\Ddd2\Extensions\Ddd2UserProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Hanoivip\Ddd2\Services\DddAuthen;
use Hanoivip\Ddd2\Services\IdpAuthen;
use Hanoivip\Ddd2\Extensions\DeviceTokenGuard;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/ipd.php' => config_path('ipd.php'),
            __DIR__ . '/../config/ddd2.php' => config_path('ddd2.php'),
        ]);
        $this->loadRoutesFrom(__DIR__ . '/../route/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../route/routes.php');
        $this->loadViewsFrom(__DIR__ . '/../views', 'hanoivip');
        $this->loadTranslationsFrom( __DIR__.'/../lang', 'hanoivip');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // add custom guard: IDddAuthen with 2fa
        Auth::extend('device_token', function ($app, $name, array $config) {
            $userProvider = app("Ddd2UserProvider");
            $request = app('request');
            return new DeviceTokenGuard($userProvider, $request, $config);
        });
        Auth::provider('ipd_users', function ($app, array $config) {
            return new Ddd2UserProvider($app->make(IDddAuthen::class));
        });
    }
    
    public function register()
    {
        if (config('ddd2.auth') == 'ipd')
        {
            $this->app->bind(IDddAuthen::class, IdpAuthen::class);
            $this->app->bind('Ddd2UserProvider', Ddd2UserProvider::class);
        }
        else
        {
            $this->app->bind(IDddAuthen::class, DddAuthen::class);
            $this->app->bind('Ddd2UserProvider', Ddd2UserProvider::class);
        }
    }
}
