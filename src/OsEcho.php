<?php

namespace Aybarsm\Laravel\OsEcho;

use Aybarsm\Laravel\Support\Enums\HttpMethod;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class OsEcho extends AbstractOsEcho
{
    use Conditionable, Macroable;

    protected object $handlers;

    protected Collection $endpoints;

    protected HttpMethod $httpMethod;

    protected object $logging;

    protected PendingRequest $pendingRequest;

    public function getPendingRequest(): PendingRequest
    {
        return $this->pendingRequest;
    }

    public function getRequestMethod(): string
    {
        return $this->httpMethod->value;
    }

    public function getEndpoints(): Collection
    {
        return $this->endpoints;
    }

    public function getHandler(string $name): ?string
    {
        return $this->handlers->{$name} ?? null;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logging->enabled ? Log::channel($this->logging->channel) : null;
    }

    public static function buildDefaultLogContext(TransferStats $transferStats): array
    {
        return [
            'handler' => static::buildHandlerLogContext($transferStats),
            'request' => static::buildRequestLogContext($transferStats->getRequest()),
            'response' => $transferStats->hasResponse() ? static::buildResponseLogContext($transferStats->getResponse()) : null,
        ];
    }

    public static function buildExceptionLogContext(TransferStats $transferStats, RequestException|ConnectionException $exception): array
    {
        $exception = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];

        return array_merge(
            static::buildDefaultLogContext($transferStats),
            [
                'exception' => $exception,
            ]
        );
    }

    protected function resolveHandlers(array $handlers): void
    {
        if (isset($this->handlers)) {
            return;
        }
        $this->handlers = (object) array_merge(
            ['request' => null, 'response' => null, 'exception' => null],
            Arr::where(
                Arr::only($handlers, ['request', 'response', 'exception']),
                fn ($class, $key): bool => is_string($class) && class_exists($class) && method_exists($class, '__invoke')
            )
        );
    }

    protected function resolveRequestConfig(array $requestConfig): void
    {
        $requestConfig['endpoints'] = is_string($requestConfig['endpoints'] ?? null) ? $requestConfig['endpoints'] : '';
        $endpoints = preg_split('/,/', $requestConfig['endpoints'], -1, PREG_SPLIT_NO_EMPTY);

        $this->endpoints = collect(array_unique(Arr::where($endpoints, fn ($val, $key) => Str::isUrl($val))));

        $requestConfig['method'] = $requestConfig['method'] ?? HttpMethod::GET;
        $this->httpMethod = match (true) {
            $requestConfig['method'] instanceof HttpMethod => $requestConfig['method'],
            HttpMethod::getFirst($requestConfig['method']) instanceof HttpMethod => HttpMethod::getFirst($requestConfig['method']),
            default => HttpMethod::GET
        };
    }

    protected function setPendingRequest(array $requestConfig): void
    {
        $request = new PendingRequest(
            factory: $requestConfig['factory'] ?? null,
            middleware: $requestConfig['middleware'] ?? []
        );

        foreach (($requestConfig['options'] ?? []) as $method => $parameters) {
            if (! method_exists($request, $method)) {
                continue;
            }

            $parameters = [$parameters];

            $request = $request->{$method}(...$parameters);
        }

        $this->pendingRequest = $request;
    }

    protected function resolveLoggingConfig(array $loggingConfig): void
    {
        $this->logging = (object) [
            'channel' => $channel = (config("logging.channels.{$loggingConfig['channel']}") ? $loggingConfig['channel'] : config('logging.default')),
            'enabled' => $channel && $loggingConfig['enabled'] === true,
            'isUsingDefault' => $channel === config('logging.default'),
        ];
    }

    protected static function buildHandlerLogContext(TransferStats $transferStats): array
    {
        return [
            'stats' => $transferStats->getHandlerStats(),
        ];
    }

    protected static function buildRequestLogContext(RequestInterface $request): array
    {
        return [
            'uri' => $request->getUri()->__toString(),
            'method' => $request->getMethod(),
            'headers' => $request->getHeaders(),
            'body' => $request->getBody()->__toString(),
            'protocolVersion' => $request->getProtocolVersion(),
        ];
    }

    protected static function buildResponseLogContext(ResponseInterface $response): array
    {
        return [
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
            'body' => $response->getBody(),
        ];
    }
}
