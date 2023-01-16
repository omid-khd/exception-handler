<?php

declare(strict_types=1);

namespace ExceptionHandler\Http;

use ExceptionHandler\Http\Controller\ControllerInterface;
use ExceptionHandler\Metadata\ExceptionMetadata;
use Psr\Http\Message\MessageInterface;
use Throwable;

final class HttpResponseMiddleware
{
    private HttpRequestProviderInterface $httpRequestProvider;
    private ControllerInterface $controller;

    public function __construct(HttpRequestProviderInterface $httpRequestProvider, ControllerInterface $controller)
    {
        $this->httpRequestProvider = $httpRequestProvider;
        $this->controller = $controller;
    }

    public function __invoke(Throwable $e, callable $next): MessageInterface
    {
        $metadata = $next($e);

        assert($metadata instanceof ExceptionMetadata);

        $request = $this->httpRequestProvider->getHttpRequest();

        return ($this->controller)($request, $metadata);
    }
}
