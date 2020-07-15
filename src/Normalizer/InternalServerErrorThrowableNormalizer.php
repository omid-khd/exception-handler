<?php

namespace Khadem\ExceptionHandler\Normalizer;

final class InternalServerErrorThrowableNormalizer implements ThrowableNormalizerInterface
{
    public function normalize(\Throwable $throwable): NormalizedThrowable
    {
        return new NormalizedThrowable('Internal Server Error', 500, $throwable);
    }
}