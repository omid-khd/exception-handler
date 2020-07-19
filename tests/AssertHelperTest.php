<?php

namespace Khadem\Tests\ExceptionHandler;

use Khadem\ExceptionHandler\AssertHelper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class AssertHelperTest
 */
final class AssertHelperTest extends MockeryTestCase
{
    public function test_it_dont_throw_exception_if_given_object_is_instanceof_given_class()
    {
        $object = new \stdClass();

        AssertHelper::assertInstanceof(\stdClass::class, $object);

        $this->assertTrue(true);
    }

    public function test_it_throw_exception_if_object_is_not_instanceof_given_class()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Expected instance of %s got %s', \Exception::class, \stdClass::class));

        AssertHelper::assertInstanceof(\Exception::class, new \stdClass());
    }

    public function test_it_call_given_fail_callback()
    {
        $object = new \stdClass();
        AssertHelper::assertInstanceof(\Exception::class, $object, function ($subject) use ($object) {
            $this->assertSame($object, $subject);
        });
    }

    public function test_it_determine_value_type()
    {
        $this->assertEquals(\stdClass::class, AssertHelper::determineType(new \stdClass()));
        $this->assertEquals('array', AssertHelper::determineType([]));
    }
}
