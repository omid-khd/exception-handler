<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler;

use Exception;
use ExceptionHandler\ExceptionHandler;
use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Metadata\ExceptionMetadataLoader;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ExceptionHandlerTest extends TestCase
{
    public function testLastMiddlewareLoadesExceptionMetadata(): void
    {
        $e = new Exception('Error');

        $metadataLoader = $this->createMock(ExceptionMetadataLoader::class);
        $metadataLoader->expects($this->once())
                       ->method('loadMetadata')
                       ->with($e)
                       ->willReturn(new ExceptionMetadata(500, 'Internal Server Error', $e));

        $exceptionHandler = new ExceptionHandler($metadataLoader);

        $this->assertInstanceOf(ExceptionMetadata::class, $exceptionHandler->handle($e));
    }
    public function testItCreateAMiddlewareAndCallEachMiddleware(): void
    {
        $e = new Exception('Error');

        $metadataLoader = $this->createMock(ExceptionMetadataLoader::class);
        $metadataLoader->expects($this->once())
                       ->method('loadMetadata')
                       ->with($e)
                       ->willReturn(new ExceptionMetadata(500, 'Internal Server Error', $e));

        $middleware = function (Throwable $e, callable $next) {
            $result = $next($e);

            $this->assertInstanceOf(ExceptionMetadata::class, $result);

            return $result;
        };

        $exceptionHandler = new ExceptionHandler($metadataLoader, [$middleware]);

        $exceptionHandler->handle($e);
    }
}
