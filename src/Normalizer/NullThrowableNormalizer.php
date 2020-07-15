<?php

namespace Khadem\ExceptionHandler\Normalizer;

final class NullThrowableNormalizer implements ThrowableNormalizerInterface
{
    public function normalize(\Throwable $throwable): NormalizedThrowable
    {
        return new NormalizedThrowable($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}