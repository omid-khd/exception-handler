<?php

namespace Khadem\ExceptionHandler\Normalizer;

use Khadem\ExceptionHandler\ClassHierarchyTrait;
use Khadem\ExceptionHandler\Exception\UnexpectedCallableResultFormatException;
use Khadem\ExceptionHandler\Exception\UnexpectedMappingFormatException;

final class MappedListThrowableNormalizer implements ThrowableNormalizerInterface
{
    use ClassHierarchyTrait;

    private $mapping;

    private $fallbackNormalizer;

    public function __construct(array $mapping = [], ThrowableNormalizerInterface $fallbackNormalizer = null)
    {
        $this->mapping = $mapping;
        $this->fallbackNormalizer = $fallbackNormalizer ?? new InternalServerErrorThrowableNormalizer();
    }

    public function normalize(\Throwable $throwable): NormalizedThrowable
    {
        foreach ($this->getClassHierarchy($throwable) as $class) {
            if (!isset($this->mapping[$class])) {
                continue;
            }

            if (is_callable($this->mapping[$class])) {
                $result = ($this->mapping[$class])($throwable);

                if ($this->hasUnexpectedFormat($result)) {
                    throw UnexpectedCallableResultFormatException::fromResult($result);
                }

                $this->mapping[$class] = array_values($result);
            }

            if ($this->hasUnexpectedFormat($this->mapping[$class])) {
                throw UnexpectedMappingFormatException::forClass($class);
            }

            list($code, $message) = $this->mapping[$class];

            return new NormalizedThrowable($message, $code, $throwable);
        }

        return $this->fallbackNormalizer->normalize($throwable);
    }

    private function hasUnexpectedFormat($result): bool
    {
        return !is_array($result) || count($result) !== 2 || !is_int($result[0]) || !is_string($result[1]);
    }
}