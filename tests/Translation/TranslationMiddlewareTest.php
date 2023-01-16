<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation;

use Exception;
use ExceptionHandler\Metadata\ExceptionMetadata;
use ExceptionHandler\Translation\TranslationConfigLoader;
use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use ExceptionHandler\Translation\TranslationMiddleware;
use ExceptionHandler\Translation\TranslatorInterface;
use PHPUnit\Framework\TestCase;

final class TranslationMiddlewareTest extends TestCase
{
    public function testItReturnMetadataAsIsIfNotAbleToLoadTranslationConfig(): void
    {
        $e = new Exception('Error');
        $metadata = new ExceptionMetadata(500, 'Internal Server Error', $e);
        $next = static fn () => $metadata;

        $middleware = new TranslationMiddleware(
            new TranslationConfigLoader(),
            $this->createMock(TranslatorInterface::class)
        );

        $this->assertSame($metadata, $middleware($e, $next));
    }

    public function testItReturnUpdatedMetadataWhenThereIsATranslationConfigForGivenThrowable(): void
    {
        $e = new Exception('Error');
        $metadata = new ExceptionMetadata(500, 'Internal Server Error', $e);
        $next = static fn () => $metadata;

        $loader = $this->createMock(TranslationConfigLoaderInterface::class);
        $loader->expects($this->once())->method('supports')->willReturn(true);
        $loader->expects($this->once())->method('load')->willReturn(new TranslationConfig('e_id', []));

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())->method('trans')->with('e_id', [], null, null)->willReturn('Translated Message');

        $middleware = new TranslationMiddleware(
            new TranslationConfigLoader([$loader]),
            $translator
        );

        $newMetadata = $middleware($e, $next);

        $this->assertNotSame($metadata, $newMetadata);
        $this->assertEquals('Translated Message', $newMetadata->getMessage());
    }
}
