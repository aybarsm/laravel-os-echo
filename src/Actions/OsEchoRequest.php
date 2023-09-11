<?php

namespace Aybarsm\Laravel\OsEcho\Actions;

use Aybarsm\Laravel\OsEcho\Contracts\OsEchoInterface;
use GuzzleHttp\TransferStats;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class OsEchoRequest
{
    public function __invoke(Application $app, OsEchoInterface $echo): void
    {
        $request = $echo->getPendingRequest()->throw(fn (): bool => true);
        foreach ($echo->getEndpoints() as $url) {
            $attempt = (object) [
                'transferStats' => null,
                'response' => null,
            ];
            try {
                $attempt->response = $request->send(
                    method: $echo->getRequestMethod(),
                    url: $url,
                    options: [
                        'on_stats' => function (TransferStats $transferStats) use ($attempt) {
                            $attempt->transferStats = $transferStats;

                            return true;
                        },
                    ]);

                if ($handler = $echo->getHandler('response')) {
                    $app->call($handler, [
                        'transferStats' => $attempt->transferStats,
                        'response' => $attempt->response,
                    ]);
                }

                break;
            } catch (RequestException|ConnectionException $exception) {
                if ($handler = $echo->getHandler('exception')) {
                    $app->call($handler, [
                        'transferStats' => $attempt->transferStats,
                        'exception' => $exception,
                    ]);
                }
            }
        }
    }
}
