<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Http;

use Exception;
use ExceptionHandler\Http\Controller\ControllerInterface;
use ExceptionHandler\Http\HttpRequestProviderInterface;
use ExceptionHandler\Http\HttpResponseMiddleware;
use ExceptionHandler\Metadata\ExceptionMetadata;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;

final class HttpResponseMiddlewareTest extends TestCase
{
    public function testItCallControllerAndReturnResponse(): void
    {
        $request = $this->createMock(MessageInterface::class);

        $e = new Exception();
        $response = $this->createMock(MessageInterface::class);
        $metadata = new ExceptionMetadata(500, 'Internal Server Error', $e);

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $controller = $this->createMock(ControllerInterface::class);
        $controller->expects($this->once())->method('__invoke')->with($request, $metadata)->willReturn($response);

        $middleware = new HttpResponseMiddleware($httpRequestProvider, $controller);

        $result = $middleware($e, function (Exception $e) use ($metadata) {
            return $metadata;
        });

        $this->assertSame($response, $result);
    }
}
