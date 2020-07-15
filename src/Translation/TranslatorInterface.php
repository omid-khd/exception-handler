<?php

namespace Khadem\ExceptionHandler\Translation;

interface TranslatorInterface
{
    public function translate(TranslatableThrowableInterface $throwable): string;
}