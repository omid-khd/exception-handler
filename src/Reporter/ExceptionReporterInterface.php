<?php

namespace Khadem\ExceptionHandler\Reporter;

interface ExceptionReporterInterface
{
    public function report(\Throwable $throwable);
}