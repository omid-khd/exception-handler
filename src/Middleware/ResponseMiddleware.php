<?php

namespace Khadem\ExceptionHandler\Middleware;

use Khadem\ExceptionHandler\Exception\UnexpectedControllerResultException;
use Khadem\ExceptionHandler\Exception\UnexpectedResponseProcessorResultException;
use Khadem\ExceptionHandler\AssertHelper;
use Khadem\ExceptionHandler\Processor\PostResponse\PostResponseProcessorInterface;
use Khadem\ExceptionHandler\Processor\PreResponse\PreResponseProcessorInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ResponseMiddleware
{
    private $controller;

    private $preResponseProcessors;

    private $postResponseProcessors;

    public function __construct(callable $controller, iterable $preResponseProcessors = null, iterable $postResponseProcessors = null)
	{
        $this->controller = $controller;
        $this->preResponseProcessors = $preResponseProcessors ?? [];
        $this->postResponseProcessors = $postResponseProcessors ?? [];
    }

    public function __invoke(\Throwable $throwable, RequestInterface $request, callable $next)
    {
        foreach ($this->preResponseProcessors as $preProcessor) {
            AssertHelper::assertInstanceof(PreResponseProcessorInterface::class, $preProcessor);

            $throwable = $preProcessor->preProcess($throwable);
        }

        $response = ($this->controller)($throwable, $request);

        AssertHelper::assertInstanceof(ResponseInterface::class, $response, static function ($response) {
            throw UnexpectedControllerResultException::fromResult($response);
        });

        foreach ($this->postResponseProcessors as $postProcessor) {
            AssertHelper::assertInstanceof(PostResponseProcessorInterface::class, $postProcessor);

            $response = $postProcessor->postProcess($response, $request);
        }

        return $response;
    }
}