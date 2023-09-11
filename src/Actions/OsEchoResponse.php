<?php

namespace Aybarsm\Laravel\OsEcho\Actions;

use Aybarsm\Laravel\OsEcho\Contracts\OsEchoInterface;
use GuzzleHttp\TransferStats;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Response;

class OsEchoResponse
{
    protected string $logMessage = 'OS Echo Successful Connection ::';

    public function __invoke(
        Application $app,
        OsEchoInterface $echo,
        TransferStats $transferStats,
        Response $response
    ): void {
        $echo->getLogger()?->info(
            $this->logMessage,
            $echo::buildDefaultLogContext($transferStats)
        );
    }
}
