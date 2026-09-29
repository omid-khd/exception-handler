<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation;

use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class TranslationConfigLoader
{
    /**
     * @var list<TranslationConfigLoaderInterface>
     */
    private readonly array $configLoaders;

    /**
     * @param iterable<TranslationConfigLoaderInterface> $configLoaders
     */
    public function __construct(iterable $configLoaders = [])
    {
        $loaders = [];

        foreach ($configLoaders as $configLoader) {
            Assert::isInstanceOf($configLoader, TranslationConfigLoaderInterface::class);
            $loaders[] = $configLoader;
        }

        $this->configLoaders = $loaders;
    }

    public function load(Throwable $e): ?TranslationConfig
    {
        foreach ($this->configLoaders as $configLoader) {
            if ($configLoader->support($e)) {
                return $configLoader->load($e);
            }
        }

        return null;
    }
}
