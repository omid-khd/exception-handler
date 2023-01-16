<?php

declare(strict_types=1);

namespace ExceptionHandler\Http;

use ExceptionHandler\Http\Controller\ControllerInterface;
use ExceptionHandler\Metadata\ExceptionMetadata;
use Psr\Http\Message\MessageInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class HttpResponseMiddleware
{
    public function __construct(
        private readonly HttpRequestProviderInterface $httpRequestProvider,
        private readonly ControllerInterface $controller,
    ) {
    }

    public function __invoke(Throwable $e, callable $next): MessageInterface
    {
        $metadata = $next($e);

        Assert::isInstanceOf($metadata, ExceptionMetadata::class);

        $request = $this->httpRequestProvider->getHttpRequest();

        return ($this->controller)($request, $metadata);
    }
}
