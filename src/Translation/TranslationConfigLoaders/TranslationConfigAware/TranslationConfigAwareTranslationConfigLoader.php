<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigAware;

use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use Throwable;

final class TranslationConfigAwareTranslationConfigLoader implements TranslationConfigLoaderInterface
{
    public function supports(Throwable $e): bool
    {
        return $e instanceof TranslationConfigAwareInterface;
    }

    public function load(Throwable $e): TranslationConfig
    {
        assert($e instanceof TranslationConfigAwareInterface);

        return $e->getTranslationConfig();
    }
}
