<?php

namespace Khadem\ExceptionHandler\Swapper;

final class NullThrowableSwapper
{
    public function __invoke(\Throwable $throwable): \Throwable
    {
        return $throwable;
    }
}