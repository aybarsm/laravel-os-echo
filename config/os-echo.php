<?php

use Aybarsm\Laravel\Support\Enums\HttpMethod;

return [
    //  Concrete classes to be used
    'concretes' => [
        'OsEcho' => Aybarsm\Laravel\OsEcho\OsEcho::class,
        'OsEchoCommand' => Aybarsm\Laravel\OsEcho\Console\Commands\OsEchoCommand::class,
    ],
    //  Handlers for action forwarding
    'handlers' => [
        'request' => Aybarsm\Laravel\OsEcho\Actions\OsEchoRequest::class,
        'response' => Aybarsm\Laravel\OsEcho\Actions\OsEchoResponse::class,
        'exception' => Aybarsm\Laravel\OsEcho\Actions\OsEchoException::class,
    ],
    'request' => [
        // Multiple endpoints can be identified by comma seperated string for fallback procedure
        'endpoints' => senv('OS_ECHO_MASTERS', senv('OS_ECHO_MASTER', '')),
        'method' => HttpMethod::GET,
        // Constructor instance, middleware and settings for Illuminate\Http\Client\PendingRequest
        'factory' => null,
        'middleware' => [],
        'options' => [
            'timeout' => 5,
            'connectTimeout' => 15,
            'withToken' => senv('OS_ECHO_TOKEN', ''),
            'acceptJson' => null,
            'withUserAgent' => 'Aybarsm OsEcho/2023 ('.config('app.name').'; '.PHP_OS.')',
            'withHeaders' => [
                'X_OS_ECHO_ID' => senv('OS_ECHO_ID', ''),
            ],
        ],
    ],
    // Logging settings
    'logging' => [
        'enabled' => (bool) env('OS_ECHO_LOG_ENABLED', false),
        'channel' => env('OS_ECHO_LOG_CHANNEL'),
    ],
];
