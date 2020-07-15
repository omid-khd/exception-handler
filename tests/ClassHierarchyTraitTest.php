<?php

namespace Khadem\Tests\ExceptionHandler;

use Khadem\ExceptionHandler\ClassHierarchyTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ClassHierarchyTraitTest extends MockeryTestCase
{
    use ClassHierarchyTrait;

    public function test_it_gets_class_hierarchy()
    {
        $classHierarchy = [
            'InvalidArgumentException',
            'LogicException',
            'Exception',
            'Throwable',
        ];

        foreach ($this->getClassHierarchy(new \InvalidArgumentException()) as $class) {
            $this->assertTrue(in_array($class, $classHierarchy));
        }
    }
}