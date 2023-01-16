<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation;

use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class TranslationConfigLoader
{
    public function __construct(private readonly iterable $configLoaders = [])
    {
    }

    public function load(Throwable $e): ?TranslationConfig
    {
        foreach ($this->configLoaders as $configLoader) {
            Assert::isInstanceOf($configLoader, TranslationConfigLoaderInterface::class);

            if ($configLoader->support($e)) {
                return $configLoader->load($e);
            }
        }

        return null;
    }
}
