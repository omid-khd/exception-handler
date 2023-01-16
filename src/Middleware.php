<?php

declare(strict_types=1);

namespace ExceptionHandler;

use Closure;

final class Middleware
{
    private Closure $executionChain;

    public function __construct(array $middlewares = [])
    {
        $lastChain = static fn ($subject) => $subject;

        while ($middleware = array_pop($middlewares)) {
            $lastChain = static fn ($subject) => $middleware($subject, $lastChain);
        }

        $this->executionChain = $lastChain;
    }

    public function handle($subject)
    {
        return ($this->executionChain)($subject);
    }
}
