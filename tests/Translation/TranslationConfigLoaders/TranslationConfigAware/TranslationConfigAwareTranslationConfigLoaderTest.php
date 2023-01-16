<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigAware;

use Exception;
use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigAware\TranslationConfigAwareInterface;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigAware\TranslationConfigAwareTranslationConfigLoader;
use PHPUnit\Framework\TestCase;

final class TranslationConfigAwareTranslationConfigLoaderTest extends TestCase
{
    public function testItSupportsInstancesOfTranslationConfigAwareInterface(): void
    {
        $e = new Exception('Error');

        $loader = new TranslationConfigAwareTranslationConfigLoader();
        $this->assertFalse($loader->supports($e));

        $configAwareException = new class extends Exception implements TranslationConfigAwareInterface {
            public function getTranslationConfig(): TranslationConfig
            {
            }
        };

        $this->assertTrue($loader->supports($configAwareException));
    }

    public function testItDelegateLoadingToTranslationConfigAwareInstance(): void
    {
        $e = new Exception('Error');

        $loader = new TranslationConfigAwareTranslationConfigLoader();
        $this->assertFalse($loader->supports($e));

        $configAwareException = new class extends Exception implements TranslationConfigAwareInterface {
            public function getTranslationConfig(): TranslationConfig
            {
                return new TranslationConfig('trans_id');
            }
        };

        $config = $loader->load($configAwareException);

        $this->assertEquals('trans_id', $config->id);
    }
}
