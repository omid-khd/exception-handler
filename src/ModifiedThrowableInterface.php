<?php

namespace Khadem\ExceptionHandler;

use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Khadem\ExceptionHandler\Translation\TranslatedThrowable;

interface ModifiedThrowableInterface
{
    public function getModifiedThrowable(): \Throwable;
}