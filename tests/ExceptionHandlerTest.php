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
    public function testLastMiddlewareLoadsExceptionMetadata(): void
    {
        $e = new Exception('Error');
        $exceptionMetadata = new ExceptionMetadata(500, 'Internal Server Error', $e);

        $metadataLoader = $this->createMock(ExceptionMetadataLoader::class);
        $metadataLoader->expects(self::once())
                       ->method('loadMetadata')
                       ->with($e)
                       ->willReturn($exceptionMetadata);

        $exceptionHandler = new ExceptionHandler($metadataLoader);
        $loadedMetadata = $exceptionHandler->handle($e);

        self::assertInstanceOf(ExceptionMetadata::class, $loadedMetadata);
        self::assertSame($exceptionMetadata, $loadedMetadata);
    }
    public function testItCreateAMiddlewareAndCallEachMiddleware(): void
    {
        $e = new Exception('Error');

        $metadataLoader = $this->createMock(ExceptionMetadataLoader::class);
        $metadataLoader->expects(self::once())
                       ->method('loadMetadata')
                       ->with($e)
                       ->willReturn(new ExceptionMetadata(500, 'Internal Server Error', $e));

        $middleware = static function (Throwable $e, callable $next) {
            $result = $next($e);

            self::assertInstanceOf(ExceptionMetadata::class, $result);

            return $result;
        };

        $exceptionHandler = new ExceptionHandler($metadataLoader, [$middleware]);

        $exceptionHandler->handle($e);
    }
}
