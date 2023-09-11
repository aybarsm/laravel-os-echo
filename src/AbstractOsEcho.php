<?php

namespace Aybarsm\Laravel\OsEcho;

use Aybarsm\Laravel\OsEcho\Contracts\OsEchoInterface;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractOsEcho implements OsEchoInterface
{
    public function __construct(
        array $handlers,
        array $requestConfig,
        array $loggingConfig,
    ) {
        $this->resolveHandlers($handlers);
        $this->resolveRequestConfig($requestConfig);
        $this->setPendingRequest($requestConfig);
        $this->resolveLoggingConfig($loggingConfig);
    }

    abstract protected function resolveHandlers(array $handlers): void;

    abstract protected function resolveRequestConfig(array $requestConfig): void;

    abstract protected function setPendingRequest(array $requestConfig): void;

    abstract protected function resolveLoggingConfig(array $loggingConfig): void;

    abstract protected static function buildHandlerLogContext(TransferStats $transferStats): array;

    abstract protected static function buildRequestLogContext(RequestInterface $request): array;

    abstract protected static function buildResponseLogContext(ResponseInterface $response): array;
}
