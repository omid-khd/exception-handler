<?php

namespace Khadem\ExceptionHandler\Translation;

use Khadem\ExceptionHandler\ModifiedThrowableInterface;

final class TranslatedThrowable extends \Exception implements ModifiedThrowableInterface
{
    public function __construct(string $message, \Throwable $previous)
    {
        parent::__construct($message, $previous->getCode(), $previous);
    }

    public function getModifiedThrowable(): \Throwable
    {
        return $this->getPrevious();
    }
}