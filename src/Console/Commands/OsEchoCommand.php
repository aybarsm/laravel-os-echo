<?php

namespace Aybarsm\Laravel\OsEcho\Console\Commands;

use Aybarsm\Laravel\OsEcho\Contracts\OsEchoInterface;
use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;

class OsEchoCommand extends Command
{
    protected $signature = 'os:echo';

    protected $description = 'Echo request to master endpoints.';

    public function handle(Application $app, OsEchoInterface $echo): int
    {
        if ($handler = $echo->getHandler('request')) {
            $this->info("Request is being forwarded to it's handler.");
            $app->call($handler);
            $this->info('Completed.');

            return 0;
        } else {
            $this->error('Request handler not found!');

            return 1;
        }
    }
}
