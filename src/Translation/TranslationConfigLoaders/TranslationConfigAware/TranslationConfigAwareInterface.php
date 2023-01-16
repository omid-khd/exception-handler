<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders\TranslationConfigAware;

use ExceptionHandler\Translation\TranslationConfig;
use Throwable;

interface TranslationConfigAwareInterface extends Throwable
{
    public function getTranslationConfig(): TranslationConfig;
}