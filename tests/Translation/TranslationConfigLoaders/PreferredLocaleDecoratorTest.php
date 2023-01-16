<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation\TranslationConfigLoaders;

use Exception;
use ExceptionHandler\Translation\Locale\PreferredLocaleProviderInterface;
use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\PreferredLocaleDecorator;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use PHPUnit\Framework\TestCase;

final class PreferredLocaleDecoratorTest extends TestCase
{
    public function testItDelegateSupportMethodToDecoratedLoader(): void
    {
        $decoratedLoader = $this->createMock(TranslationConfigLoaderInterface::class);
        $decoratedLoader->expects($this->exactly(2))->method('supports')->willReturnOnConsecutiveCalls(true, false);
        $loader = new PreferredLocaleDecorator($decoratedLoader, $this->createMock(PreferredLocaleProviderInterface::class));

        $exception = new Exception('Error');

        $this->assertTrue($loader->supports($exception));
        $this->assertFalse($loader->supports($exception));
    }

    public function testItSetLocaleToEnglishIfNoPreferedLocaleCanBeLoaded(): void
    {
        $preferredLocaleProvider = $this->createMock(PreferredLocaleProviderInterface::class);
        $preferredLocaleProvider->expects($this->once())->method('getPreferredLocale')->willReturn(null);

        $decoratedLoader = $this->createMock(TranslationConfigLoaderInterface::class);
        $decoratedLoader->expects($this->once())->method('load')->willReturn(new TranslationConfig('trans_id'));
        $loader = new PreferredLocaleDecorator($decoratedLoader, $preferredLocaleProvider);

        $exception = new Exception('Error');

        $config = $loader->load($exception);
        $this->assertEquals('en', $config->locale);
    }

    public function testItSetLocaleToPreferredLocale(): void
    {
        $preferredLocaleProvider = $this->createMock(PreferredLocaleProviderInterface::class);
        $preferredLocaleProvider->expects($this->once())->method('getPreferredLocale')->willReturn('nl_NL');

        $decoratedLoader = $this->createMock(TranslationConfigLoaderInterface::class);
        $decoratedLoader->expects($this->once())->method('load')->willReturn(new TranslationConfig('trans_id'));
        $loader = new PreferredLocaleDecorator($decoratedLoader, $preferredLocaleProvider);

        $exception = new Exception('Error');

        $config = $loader->load($exception);
        $this->assertEquals('nl_NL', $config->locale);
    }
}
