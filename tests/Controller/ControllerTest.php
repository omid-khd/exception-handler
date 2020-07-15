<?php

namespace Khadem\Tests\ExceptionHandler\Controller;

use Khadem\ExceptionHandler\Controller\Controller;
use Khadem\ExceptionHandler\Controller\Renderer\ControllerRendererInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ControllerTest
 */
final class ControllerTest extends MockeryTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test_it_convert_throwable_to_response($code, $message, $httpStatusCode)
    {
        $body          = \Mockery::mock(StreamInterface::class);
        $streamFactory = \Mockery::mock(StreamFactoryInterface::class);
        $streamFactory->shouldReceive('createStream')
                      ->once()
                      ->with('')
                      ->andReturn($body);

        $_response = \Mockery::mock(ResponseInterface::class);
        $_response->shouldReceive('withBody')
                  ->once()
                  ->with($body)
                  ->andReturn(\Mockery::mock(ResponseInterface::class));

        $responseFactory = \Mockery::mock(ResponseFactoryInterface::class);
        $responseFactory->shouldReceive('createResponse')
                        ->once()
                        ->with($httpStatusCode, $message)
                        ->andReturn($_response);

        $throwable = new \Exception('Custom Message', $code);

        $renderer  = \Mockery::mock(ControllerRendererInterface::class);
        $renderer->shouldReceive('render')->once()->with($throwable)->andReturn('');

        $controller = new Controller($responseFactory, $streamFactory, $renderer);
        $response   = $controller($throwable, \Mockery::mock(RequestInterface::class));

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function dataProvider()
    {
        return [
            [400, 'Bad Request', 400],
            [0, 'Internal Server Error', 500],
        ];
    }
}
