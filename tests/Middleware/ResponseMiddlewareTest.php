<?php

namespace Khadem\Tests\ExceptionHandler\Middleware;

use Khadem\ExceptionHandler\Exception\UnexpectedControllerResultException;
use Khadem\ExceptionHandler\Middleware\ResponseMiddleware;
use Khadem\ExceptionHandler\Processor\PostResponse\PostResponseProcessorInterface;
use Khadem\ExceptionHandler\Processor\PreResponse\PreResponseProcessorInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseMiddlewareTest
 */
final class ResponseMiddlewareTest extends MockeryTestCase
{
    public function test_it_throw_exception_if_pre_processor_is_not_instanceof_pre_response_processor()
    {
        $expectedMessage = sprintf('Expected instance of %s got %s', PreResponseProcessorInterface::class, 'Closure');
        $controller      = static function () {};
        $preProcessors   = [function () {}];
        $middleware      = new ResponseMiddleware($controller, $preProcessors);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $middleware(new \Exception(), \Mockery::mock(RequestInterface::class), function () {});
    }

    public function test_it_delegate_converting_throwable_to_response_to_controller()
    {
        $response   = \Mockery::mock(ResponseInterface::class);
        $controller = static function (\Throwable $throwable) use ($response) {
            return $response;
        };

        $middleware = new ResponseMiddleware($controller, [new NullPreResponseProcessor()], [new NullPostResponseProcessor()]);

        $throwable = new \Exception();
        $request   = \Mockery::mock(RequestInterface::class);

        $result = $middleware($throwable, $request, static function ($throwable, $request) {});

        $this->assertSame($response, $result);
    }

    public function test_it_throw_exception_if_controller_result_is_not_a_response_object()
    {
        $controller = static function (\Throwable $throwable) {
            return new \stdClass();
        };

        $middleware = new ResponseMiddleware($controller);

        $throwable = new \Exception();
        $request   = \Mockery::mock(RequestInterface::class);

        $this->expectException(UnexpectedControllerResultException::class);
        $this->expectExceptionMessage(sprintf(
            'The controller must return a "%s" object but it returned %s.',
            ResponseInterface::class,
            \stdClass::class
        ));

        $middleware($throwable, $request, static function ($throwable, $request) {});
    }

    public function test_it_throw_exception_if_post_processor_result_is_not_a_response_object()
    {
        $controller = static function (\Throwable $throwable) {
            return \Mockery::mock(ResponseInterface::class);
        };

        $middleware = new ResponseMiddleware($controller, [], [function () {}]);

        $throwable = new \Exception();
        $request   = \Mockery::mock(RequestInterface::class);

        $this->expectExceptionMessage(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Expected instance of %s got %s', PostResponseProcessorInterface::class, 'Closure'));

        $middleware($throwable, $request, static function ($throwable, $request) {});
    }
}

class NullPreResponseProcessor implements PreResponseProcessorInterface
{
    public function preProcess(\Throwable $throwable): \Throwable
    {
        return $throwable;
    }
}

class NullPostResponseProcessor implements PostResponseProcessorInterface
{
    public function postProcess(ResponseInterface $response, RequestInterface $request): ResponseInterface
    {
        return $response;
    }
}