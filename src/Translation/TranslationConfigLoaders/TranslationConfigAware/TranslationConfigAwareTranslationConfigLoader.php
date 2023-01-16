<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigAware;

use ExceptionHandler\Translation\TranslationConfig;
use ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigLoaderInterface;
use Throwable;
use Webmozart\Assert\Assert;

final class TranslationConfigAwareTranslationConfigLoader implements TranslationConfigLoaderInterface
{
    public function support(Throwable $e): bool
    {
        return $e instanceof TranslationConfigAwareInterface;
    }

    public function load(Throwable $e): TranslationConfig
    {
        Assert::isInstanceOf($e, TranslationConfigAwareInterface::class);

        return $e->getTranslationConfig();
    }
}
