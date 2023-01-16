<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation\TranslationConfigLoaders;

use Exception;
use ExceptionHandler\Lib\StaticList;
use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\StaticListTranslationConfigLoader;
use PHPUnit\Framework\TestCase;

final class StaticListTranslationConfigLoaderTest extends TestCase
{
    public function testItDelegateSupportingToInnerLoader(): void
    {
        $innerLoader = $this->createMock(StaticList::class);
        $innerLoader->expects($this->once())->method('has')->willReturn(true);
        $loader = new StaticListTranslationConfigLoader($innerLoader);

        $this->assertTrue($loader->support(new Exception()));
    }

    public function testItDelegateLoadingToInnerLoader(): void
    {
        $translationConfig = new TranslationConfig('trans_id');
        $factory = static fn (): TranslationConfig => $translationConfig;

        $innerLoader = $this->createMock(StaticList::class);
        $innerLoader->expects($this->once())->method('get')->willReturn($factory);
        $loader = new StaticListTranslationConfigLoader($innerLoader);

        $this->assertSame($translationConfig, $loader->load(new Exception()));
    }
}
