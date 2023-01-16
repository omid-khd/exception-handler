<?php

namespace ExceptionHandler\Lib;

use ExceptionHandler\Exception\FactoryResolutionException;
use Psr\Container\ContainerInterface;
use Throwable;

class StaticListLoader
{
    private array $list = [];
    private ContainerInterface $factoryLocator;

    public function __construct(ContainerInterface $factoryLocator)
    {
        $this->factoryLocator = $factoryLocator;
    }

    public function setList(array $list): void
    {
        $this->list = $list;
    }

    public function supports(Throwable $e): bool
    {
        return count(array_intersect_key($this->list, $this->getClassHierarchy($e))) > 0;
    }

    public function load(Throwable $e)
    {
        $factory = $this->getFactory($e);

        return $factory($e);
    }

    private function getClassHierarchy(Throwable $e): array
    {
        $class = get_class($e);

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

        if ($type === 'string') {
            return $this->resolveFactoryFromClassFQN($class);
        }

        if ($type === 'array') {
            return $this->resolveFactoryFromArray($class);
        }

        throw FactoryResolutionException::invalidFactoryType($type);
    }

    private function resolveFactoryFromClassFQN(string $factory): callable
    {
        if(false !== strpos($factory, '@')) {
            return $this->resolveFactoryFromArray(explode('@', $factory));
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