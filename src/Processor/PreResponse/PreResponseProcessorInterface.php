<?php

namespace Khadem\ExceptionHandler\Processor\PreResponse;

interface PreResponseProcessorInterface
{
    public function preProcess(\Throwable $throwable): \Throwable;
}