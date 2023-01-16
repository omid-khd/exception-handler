<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders;

use ExceptionHandler\Lib\StaticList;
use ExceptionHandler\Translation\TranslationConfig;
use Throwable;
use Webmozart\Assert\Assert;

final readonly class StaticListTranslationConfigLoader implements TranslationConfigLoaderInterface
{
    public function __construct(private StaticList $loader)
    {
    }

    public function support(Throwable $e): bool
    {
        return $this->loader->has($e);
    }

    public function load(Throwable $e): TranslationConfig
    {
        $factory = $this->loader->get($e);
        $config = $factory($e);

        Assert::isInstanceOf($config, TranslationConfig::class);

        return $config;
    }
}
