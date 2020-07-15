<?php

namespace Khadem\Tests\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Khadem\ExceptionHandler\Processor\PreResponse\SwapThrowableTypeProcessor;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class SwapThrowableTypeProcessorTest
 */
final class SwapThrowableTypeProcessorTest extends MockeryTestCase
{
    public function test_it_throw_exception_if_given_swap_map_has_no_correct_format()
    {
        $swapMap = [1 => 1];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected class name as index for swapping throwable type got number');

        new SwapThrowableTypeProcessor($swapMap);
    }

    public function test_it_throw_exception_if_given_class_not_exists()
    {
        $swapMap = ['foo' => 1];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Class foo not exists');

        new SwapThrowableTypeProcessor($swapMap);
    }

    public function test_it_throw_exception_if_given_factory_is_not_callable()
    {
        $swapMap = [\Exception::class => 1];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected callable got integer');

        new SwapThrowableTypeProcessor($swapMap);
    }

    public function test_it_throw_exception_if_given_input_is_modified_before()
    {
        $swapThrowableTypeProcessor = new SwapThrowableTypeProcessor([\Exception::class => static function () {}]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("You can't modify throwable before swapping it's type. Try rearranging pre response processors.");

        $swapThrowableTypeProcessor->preProcess(new NormalizedThrowable('', 0, new \Exception()));
    }

    public function test_it_throw_exception_if_factory_result_is_not_instance_of_throwable()
    {
        $swapThrowableTypeProcessor = new SwapThrowableTypeProcessor([\Exception::class => static function () {
            return new \stdClass();
        }]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Expected instance of %s got %s', \Throwable::class, \stdClass::class));

        $swapThrowableTypeProcessor->preProcess(new \Exception());
    }

    public function test_it_swap_throwable_type()
    {
        $swapThrowableTypeProcessor = new SwapThrowableTypeProcessor([\Exception::class => static function () {
            return new \InvalidArgumentException();
        }]);

        $result = $swapThrowableTypeProcessor->preProcess(new \Exception());

        $this->assertInstanceOf(\InvalidArgumentException::class, $result);
    }

    public function test_it_dont_swap_throwable_type_if_swap_map_dont_contains_given_throwable_type()
    {
        $swapThrowableTypeProcessor = new SwapThrowableTypeProcessor([]);

        $result = $swapThrowableTypeProcessor->preProcess($throwable = new \Exception());

        $this->assertSame($throwable, $result);
    }
}
