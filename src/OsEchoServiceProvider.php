<?php

namespace Aybarsm\Laravel\OsEcho;

use Aybarsm\Laravel\OsEcho\Contracts\OsEchoInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class OsEchoServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/os-echo.php',
            'os-echo'
        );

        $this->app->singleton(OsEchoInterface::class, OsEcho::class);

        $this->app->alias(OsEchoInterface::class, 'os-echo');
    }

    public function boot(): void
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/os-echo.php' => config_path('os-echo.php'),
            ], 'config');
        }
    }

    public function provides(): array
    {
        return [
            OsEchoInterface::class, 'os-echo',
        ];
    }
}
