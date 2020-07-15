<?php

namespace Khadem\ExceptionHandler\Translation;

interface TranslatableThrowableInterface
{
    public function getMessageKey(): string;

    public function getMessageData();
}