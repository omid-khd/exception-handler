<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation;

use Exception;
use ExceptionHandler\Translation\TranslationConfigLoader;
use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use PHPUnit\Framework\TestCase;

final class TranslationConfigLoaderTest extends TestCase
{
    public function testItReturnNullIfNoLoaderIsPassed(): void
    {
        $translationConfigLoader = new TranslationConfigLoader([]);

        $this->assertNull($translationConfigLoader->load(new Exception('Error')));
    }

    public function testItReturnNullIfNoneOfLoadersSupportPassedThrowable(): void
    {
        $loader = $this->createMock(TranslationConfigLoaderInterface::class);
        $loader->expects($this->once())->method('supports')->willReturn(false);

        $translationConfigLoader = new TranslationConfigLoader([$loader]);

        $this->assertNull($translationConfigLoader->load(new Exception('Error')));
    }

    public function testItDelegateLoadingTranslationConfigToLoader(): void
    {
        $config = new TranslationConfig('translation_id');

        $loader = $this->createMock(TranslationConfigLoaderInterface::class);
        $loader->expects($this->once())->method('supports')->willReturn(true);
        $loader->expects($this->once())->method('load')->willReturn($config);

        $translationConfigLoader = new TranslationConfigLoader([$loader]);

        $this->assertSame($config, $translationConfigLoader->load(new Exception('Error')));
    }
}
