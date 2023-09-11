<?php

namespace Aybarsm\Laravel\OsEcho\Actions;

use Aybarsm\Laravel\OsEcho\Contracts\OsEchoInterface;
use GuzzleHttp\TransferStats;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class OsEchoException
{
    protected string $logMessage = 'OS Echo Failed to Connect Master [%s] ::';

    public function __invoke(
        Application $app,
        OsEchoInterface $echo,
        TransferStats $transferStats,
        RequestException|ConnectionException $exception
    ): void {
        $echo->getLogger()?->error(
            sprintf($this->logMessage, get_class($exception)),
            $echo::buildExceptionLogContext($transferStats, $exception)
        );
    }
}
