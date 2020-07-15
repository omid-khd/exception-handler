<?php

namespace Khadem\ExceptionHandler;

final class AssertHelper
{
    public static function assertInstanceof(string $class, $object, callable $failCallback = null)
    {
        if ($object instanceof $class) {
            return;
        }

        if (null === $failCallback) {
            $message = sprintf('Expected instance of %s got %s', $class, self::determineType($object));

            throw new \InvalidArgumentException($message);
        }

        $failCallback($object);
    }

    public static function determineType($value): string
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }
}