<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders;

use ExceptionHandler\Translation\TranslationConfig;
use Throwable;

interface TranslationConfigLoaderInterface
{
    public function supports(Throwable $e): bool;

    public function load(Throwable $e): TranslationConfig;
}