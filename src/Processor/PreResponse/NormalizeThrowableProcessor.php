<?php

namespace Khadem\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\Normalizer\ThrowableNormalizerInterface;
use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;

final class NormalizeThrowableProcessor implements PreResponseProcessorInterface
{
    private $normalizer;

    public function __construct(ThrowableNormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function preProcess(\Throwable $throwable): \Throwable
    {
        if ($throwable instanceof NormalizedThrowable) {
            return $throwable;
        }

        return $this->normalizer->normalize($throwable);
    }
}