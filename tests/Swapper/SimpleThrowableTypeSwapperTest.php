<?php

namespace Khadem\Tests\ExceptionHandler\Swapper;

use Khadem\ExceptionHandler\Swapper\SimpleThrowableTypeSwapper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class SimpleThrowableTypeSwapperTest extends MockeryTestCase
{
    public function test_it_throw_exception_if_given_input_is_not_a_class()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Class with FQN foo not found.'));

        new SimpleThrowableTypeSwapper('foo', 'bar');
    }

    public function test_it_throw_exception_if_given_input_is_not_a_throwable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Expected instance of %s got %s', \Throwable::class, \stdClass::class));

        new SimpleThrowableTypeSwapper(\stdClass::class, 'bar');
    }

    public function test_it_throw_exception_if_given_output_is_not_a_class()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Class with FQN foo not found.'));

        new SimpleThrowableTypeSwapper(\Exception::class, 'foo');
    }

    public function test_it_throw_exception_if_given_output_is_not_a_throwable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Expected instance of %s got %s', \Throwable::class, \stdClass::class));

        new SimpleThrowableTypeSwapper(\Exception::class, \stdClass::class);
    }

    public function test_it_throw_exception_if_given_throwable_is_not_of_input_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Expected instance of %s as input but throwable is of type %s',
            \Exception::class,
            \InvalidArgumentException::class
        ));

        $swapper = new SimpleThrowableTypeSwapper(\Exception::class, \LogicException::class);

        $swapper(new \InvalidArgumentException());
    }

    public function test_it_swap_throwable_type()
    {
        $message = 'Custom Message';
        $code = rand(100, 500);

        $swapper = new SimpleThrowableTypeSwapper(\Exception::class, \LogicException::class);

        $result = $swapper(new \Exception($message, $code));

        $this->assertInstanceOf(\LogicException::class, $result);
        $this->assertEquals($message, $result->getMessage());
        $this->assertEquals($code, $result->getCode());
    }
}