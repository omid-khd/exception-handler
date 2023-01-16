<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation;

use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use Throwable;

final class TranslationConfigLoader
{
    private iterable $configLoaders;

    public function __construct(iterable $configLoaders = [])
    {
        $this->configLoaders = $configLoaders;
    }

    public function load(Throwable $e): ?TranslationConfig
    {
        foreach ($this->configLoaders as $configLoader) {
            assert($configLoader instanceof TranslationConfigLoaderInterface);

            if ($configLoader->supports($e)) {
                return $configLoader->load($e);
            }
        }

        return null;
    }
}
