<?php

namespace Khadem\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\AssertHelper;
use Khadem\ExceptionHandler\ClassHierarchyTrait;
use Khadem\ExceptionHandler\ModifiedThrowableInterface;

final class SwapThrowableTypeProcessor implements PreResponseProcessorInterface
{
    use ClassHierarchyTrait;

    private $swapMap;

    public function __construct(array $swapMap)
    {
        foreach ($swapMap as $class => $factory) {
            $this->validateClassExists($class);
            $this->validateIsCallable($factory);
        }

        $this->swapMap = $swapMap;
    }

    public function preProcess(\Throwable $throwable): \Throwable
    {
        if ($throwable instanceof ModifiedThrowableInterface) {
            $msg = "You can't modify throwable before swapping it's type. Try rearranging pre response processors.";

            throw new \LogicException($msg);
        }

        foreach ($this->getClassHierarchy($throwable) as $class) {
            if (isset($this->swapMap[$class])) {
                $swapped = ($this->swapMap[$class])($throwable);

                AssertHelper::assertInstanceof(\Throwable::class, $swapped);

                return $swapped;
            }
        }

        return $throwable;
    }

    private function validateClassExists($class)
    {
        if (is_numeric($class)) {
            throw new \InvalidArgumentException('Expected class name as index for swapping throwable type got number');
        }

        if (!class_exists($class)) {
            throw new \InvalidArgumentException("Class {$class} not exists");
        }
    }

    private function validateIsCallable($factory)
    {
        if (!is_callable($factory)) {
            throw new \InvalidArgumentException('Expected callable got '.AssertHelper::determineType($factory));
        }
    }
}