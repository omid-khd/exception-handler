<?php

namespace Khadem\ExceptionHandler\Normalizer;

interface ThrowableNormalizerInterface
{
    public function normalize(\Throwable $throwable): NormalizedThrowable;
}