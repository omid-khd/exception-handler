<?php

namespace Khadem\Tests\ExceptionHandler\Swapper;

use Khadem\ExceptionHandler\Swapper\NullThrowableSwapper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class NullThrowableSwapperTest extends MockeryTestCase
{
    public function test_it_dont_swap_throwable_type()
    {
        $throwable = new \Exception();
        $swapper = new NullThrowableSwapper();

        $this->assertSame($throwable, $swapper($throwable));
    }
}