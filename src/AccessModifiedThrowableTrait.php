<?php

namespace Khadem\ExceptionHandler;

trait AccessModifiedThrowableTrait
{
    private function getModifiedThrowable(\Throwable $throwable)
    {
        if ($throwable instanceof ModifiedThrowableInterface) {
            return $this->getModifiedThrowable($throwable->getModifiedThrowable());
        }

        return $throwable;
    }
}