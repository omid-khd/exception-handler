<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation\TranslationConfigLoaders;

use Exception;
use ExceptionHandler\Lib\StaticListLoader;
use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\StaticListTranslationConfigLoader;
use PHPUnit\Framework\TestCase;

final class StaticListTranslationConfigLoaderTest extends TestCase
{
    public function testItDelegateSupportingToInnerLoader(): void
    {
        $innerLoader = $this->createMock(StaticListLoader::class);
        $innerLoader->expects($this->once())->method('supports')->willReturn(true);
        $loader = new StaticListTranslationConfigLoader($innerLoader);

        $this->assertTrue($loader->supports(new Exception()));
    }

    public function testItDelegateLoadingToInnerLoader(): void
    {
        $translationConfig = new TranslationConfig('trans_id');

        $innerLoader = $this->createMock(StaticListLoader::class);
        $innerLoader->expects($this->once())->method('load')->willReturn($translationConfig);
        $loader = new StaticListTranslationConfigLoader($innerLoader);

        $this->assertSame($translationConfig, $loader->load(new Exception()));
    }
}
