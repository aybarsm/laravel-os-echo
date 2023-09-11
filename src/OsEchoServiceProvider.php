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

        $this->publishes([
            __DIR__.'/../config/os-echo.php' => config_path('os-echo.php'),
        ], 'config');

        $concrete = sconfig('os-echo.concretes.OsEcho', \Aybarsm\Laravel\OsEcho\OsEcho::class);

        $this->app->singleton(OsEchoInterface::class,
            fn ($app) => new $concrete(
                config('os-echo.handlers', []),
                config('os-echo.request', []),
                config('os-echo.logging', [])
            )
        );

        $this->app->alias(OsEchoInterface::class, 'os-echo');

        $commandConcrete = sconfig('os-echo.concretes.OsEchoCommand', \Aybarsm\Laravel\OsEcho\Console\Commands\OsEchoCommand::class);

        $this->commands([
            $commandConcrete,
        ]);
    }

    public function provides(): array
    {
        return [
            OsEchoInterface::class, 'os-echo',
        ];
    }
}
