<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders;

use ExceptionHandler\Lib\StaticListLoader;
use ExceptionHandler\Translation\TranslationConfig;
use Throwable;

final class StaticListTranslationConfigLoader implements TranslationConfigLoaderInterface
{
    private StaticListLoader $loader;

    public function __construct(StaticListLoader $loader)
    {
        $this->loader = $loader;
    }

    public function supports(Throwable $e): bool
    {
        return $this->loader->supports($e);
    }

    public function load(Throwable $e): TranslationConfig
    {
        $config = $this->loader->load($e);

        assert($config instanceof TranslationConfig);

        return $config;
    }
}
