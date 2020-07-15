<?php

namespace Khadem\ExceptionHandler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ExceptionHandler
{
    private $middlewareChain;

    public function __construct(callable ...$middleware)
    {
        $this->middlewareChain = $this->createExecutionChain($middleware);
    }

    public function handle(\Throwable $throwable, RequestInterface $request): ResponseInterface
    {
		return ($this->middlewareChain)($throwable, $request);
    }
	
	private function createExecutionChain(array $middlewareList) : callable
    {
        $lastCallable = static function ($throwable, $request) {};

        while ($middleware = \array_pop($middlewareList)) {
            $lastCallable = static function ($throwable, $request) use ($middleware, $lastCallable) {
                return $middleware($throwable, $request,  $lastCallable);
            };
        }

        return $lastCallable;
    }
}