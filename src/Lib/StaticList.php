<?php

namespace ExceptionHandler\Lib;

use ExceptionHandler\Exception\FactoryResolutionException;
use ExceptionHandler\Metadata\ExceptionMetadata;
use Psr\Container\ContainerInterface;
use Throwable;

class StaticList
{
    /**
     * @var array<class-string, callable(Throwable $e): mixed>
     */
    private array $list = [];

    public function __construct(private readonly ContainerInterface $factoryLocator)
    {
    }

    /**
     * @param array<class-string, callable(Throwable $e): mixed> $list
     */
    public function setList(array $list): void
    {
        $this->list = $list;
    }

    public function has(Throwable $e): bool
    {
        return !empty(array_intersect_key($this->list, $this->getClassHierarchy($e)));
    }

    /**
     * @return callable(Throwable $e): ExceptionMetadata
     */
    public function get(Throwable $e): callable
    {
        return $this->getFactory($e);
    }

    private function getClassHierarchy(Throwable $e): array
    {
        $class = $e::class;

        return [$class => $class] + class_parents($class) + class_implements($class);
    }

    private function getFactory(Throwable $e): callable
    {
        $class = array_intersect_key($this->list, $this->getClassHierarchy($e));
        $class = array_shift($class);

        if (is_callable($class)) {
            return $class;
        }

        $type = gettype($class);

        return match ($type) {
            'string' => $this->resolveFactoryFromClassFqn($class),
            'array' => $this->resolveFactoryFromArray($class),
            default => throw FactoryResolutionException::invalidFactoryType($type),
        };
    }

    private function resolveFactoryFromClassFqn(string $factory): callable
    {
        if(str_contains($factory, '@')) {
            $lastAtSignPosition = strrpos($factory, '@');
            return $this->resolveFactoryFromArray([
                substr($factory, 0, $lastAtSignPosition),
                substr($factory, $lastAtSignPosition + 1),
            ]);
        }

        $factoryObject = $this->resolveFactoryObject($factory);

        if (!method_exists($factoryObject, '__invoke')) {
            throw FactoryResolutionException::classNotCallable($factory);
        }

        return [$factoryObject, '__invoke'];
    }

    private function resolveFactoryFromArray(array $factory): callable
    {
        if (count($factory) !== 2) {
            throw FactoryResolutionException::invalidCallableArray(count($factory));
        }

        [$object, $method] = array_values($factory);

        if (!is_string($object)) {
            throw FactoryResolutionException::unexpectedType('string', gettype($object));
        }

        if (!is_string($method)) {
            throw FactoryResolutionException::unexpectedType('string', gettype($method));
        }

        $factoryObject = $this->resolveFactoryObject($object);

        if (!method_exists($factoryObject, $method)) {
            throw FactoryResolutionException::methodNotFound($object, $method);
        }

        return [$factoryObject, $method];
    }

    private function resolveFactoryObject(string $serviceId): object
    {
        if ($this->factoryLocator->has($serviceId)) {
            return $this->factoryLocator->get($serviceId);
        }

        throw FactoryResolutionException::serviceNotFound($serviceId);
    }
}