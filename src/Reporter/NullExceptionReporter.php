<?php

namespace Khadem\ExceptionHandler\Reporter;

class NullExceptionReporter implements ExceptionReporterInterface
{
    public function report(\Throwable $throwable)
    {
    }
}