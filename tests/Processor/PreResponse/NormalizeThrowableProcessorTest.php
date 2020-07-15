<?php

namespace Khadem\Tests\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Khadem\ExceptionHandler\Normalizer\ThrowableNormalizerInterface;
use Khadem\ExceptionHandler\Processor\PreResponse\NormalizeThrowableProcessor;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class NormalizeThrowableProcessorTest
 */
final class NormalizeThrowableProcessorTest extends MockeryTestCase
{
    public function test_skip_normalizing_throwable_if_its_already_normalized()
    {
        $normalizer = \Mockery::mock(ThrowableNormalizerInterface::class);
        $normalizer->shouldNotReceive('normalize');

        $normalizeThrowableProcessor = new NormalizeThrowableProcessor($normalizer);

        $throwable = new NormalizedThrowable('', 0, new \Exception());

        $result = $normalizeThrowableProcessor->preProcess($throwable);

        $this->assertSame($throwable, $result);
    }
    public function test_it_ask_normalize_to_normalize_throwable()
    {
        $throwable = new \Exception();

        $normalizer = \Mockery::mock(ThrowableNormalizerInterface::class);
        $normalizer->shouldReceive('normalize')
                   ->once()
                   ->with($throwable)
                   ->andReturn(new NormalizedThrowable('', 0, $throwable));

        $normalizeThrowableProcessor = new NormalizeThrowableProcessor($normalizer);


        $result = $normalizeThrowableProcessor->preProcess($throwable);

        $this->assertInstanceOf(NormalizedThrowable::class, $result);
        $this->assertSame($throwable, $result->getModifiedThrowable());
    }
}
