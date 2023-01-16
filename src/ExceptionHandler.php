<?php

declare(strict_types=1);

namespace ExceptionHandler;

use Closure;
use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\ExceptionMetadataLoader;
use Throwable;

final class ExceptionHandler
{
    private Closure $executionChain;

    public function __construct(ExceptionMetadataLoader $metadataLoader, array $middlewares = [])
    {
        $lastChain = static fn(Throwable $e): ExceptionMetadata => $metadataLoader->loadMetadata($e);

        while ($middleware = array_pop($middlewares)) {
            $lastChain = static fn(Throwable $e) => $middleware($e, $lastChain);
        }

        $this->executionChain = $lastChain;
    }

    public function handle(Throwable $e)
    {
        return ($this->executionChain)($e);
    }
}
