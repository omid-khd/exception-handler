<?php

namespace Khadem\Tests\ExceptionHandler;

use Khadem\ExceptionHandler\AccessModifiedThrowableTrait;
use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Khadem\ExceptionHandler\Translation\TranslatedThrowable;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class AccessModifiedThrowableTraitTest extends MockeryTestCase
{
    use AccessModifiedThrowableTrait;

    public function test_it_dont_recurse_if_given_throwable_is_not_modified()
    {
        $throwable = new \Exception();

        $this->assertSame($throwable, $this->getModifiedThrowable($throwable));
    }

    public function test_it_recurse_if_given_throwable_is_modified()
    {
        $throwable         = new \Exception();
        $modifiedThrowable = new TranslatedThrowable('', new NormalizedThrowable('', 0, $throwable));

        $this->assertSame($throwable, $this->getModifiedThrowable($modifiedThrowable));
    }
}