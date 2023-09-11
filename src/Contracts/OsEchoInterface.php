<?php

namespace Aybarsm\Laravel\OsEcho\Contracts;

use GuzzleHttp\TransferStats;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

interface OsEchoInterface
{
    public function getPendingRequest(): PendingRequest;

    public function getRequestMethod(): string;

    public function getEndpoints(): Collection;

    public function getHandler(string $name): ?string;

    public function getLogger(): ?LoggerInterface;

    public static function buildDefaultLogContext(TransferStats $transferStats): array;

    public static function buildExceptionLogContext(TransferStats $transferStats, RequestException|ConnectionException $exception): array;
}
