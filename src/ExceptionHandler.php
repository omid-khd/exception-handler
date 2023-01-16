<?php

declare(strict_types=1);

namespace ExceptionHandler;

use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\ExceptionMetadataLoader;
use Throwable;

final class ExceptionHandler
{
    private Middleware $middleware;

    public function __construct(ExceptionMetadataLoader $metadataLoader, array $middlewares = [])
    {
        $middlewares[] = static fn(Throwable $e): ExceptionMetadata => $metadataLoader->loadMetadata($e);
        $this->middleware = new Middleware($middlewares);
    }

    public function handle(Throwable $e)
    {
        return $this->middleware->handle($e);
    }
}
