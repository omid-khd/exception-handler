<?php

declare(strict_types=1);

namespace ExceptionHandler\Exception;

use InvalidArgumentException;

final class FactoryResolutionException extends InvalidArgumentException
{
    public static function invalidFactoryType(string $type): self
    {
        return new FactoryResolutionException(
            sprintf('expected factory of type callable, string or array. got %s', $type)
        );
    }

    public static function classNotCallable(string $class): self
    {
        if (!class_exists($class)) {
            $msg = "Class {$class} does not exist";
        } elseif (!method_exists($class, '__invoke')) {
            $msg = "Class {$class} does not have a __invoke method";
        } else {
            $msg = sprintf('Class %s is not callable', $class);
        }

        return new FactoryResolutionException($msg);
    }

    public static function invalidCallableArray(int $itemsCount): self
    {
        return new FactoryResolutionException("Expected an array with 2 item got {$itemsCount} item(s)");
    }

    public static function unexpectedType(string $expectedType, string $actualType): self
    {
        return new FactoryResolutionException("Expected {$expectedType} got {$actualType}");
    }

    public static function methodNotFound(string $class, string $method): self
    {
        return new FactoryResolutionException(
            sprintf('Method %s is not found on %s %s', $method, class_exists($class) ? 'class' : 'service', $class)
        );
    }

    public static function serviceNotFound(string $id): self
    {
        return new FactoryResolutionException("There is no service with id {$id}");
    }

    public static function entryNotFound(string $class): self
    {
        return new FactoryResolutionException("No entry found for class {$class} in the static list");
    }
}
