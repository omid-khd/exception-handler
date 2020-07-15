<?php

namespace Khadem\Tests\ExceptionHandler;

use Khadem\ExceptionHandler\ExceptionHandler;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ExceptionHandlerTest extends MockeryTestCase
{
    public function test_it_create_execution_chain_and_handle_throwable()
    {
        $throwable = new \Exception();
        $self = $this;

        $first = static function (\Throwable $input, RequestInterface $request, callable $next) use ($throwable, $self) {
            $self->assertSame($throwable, $input);

            return $next($throwable, $request);
        };

        $response = \Mockery::mock(ResponseInterface::class);
        $second = static function () use ($response) {
            return $response;
        };

        $handler = new ExceptionHandler($first, $second);

        $this->assertSame($response, $handler->handle($throwable, \Mockery::mock(RequestInterface::class)));
    }
}