<?php

namespace Khadem\Tests\ExceptionHandler\Normalizer;

use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Khadem\ExceptionHandler\Normalizer\NullThrowableNormalizer;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class NullThrowableNormalizerTest
 */
final class NullThrowableNormalizerTest extends MockeryTestCase
{
    public function test_it_normalize_throwable()
    {
        $throwable  = new \Exception('Custom Message', 400);
        $normalizer = new NullThrowableNormalizer();
        $result     = $normalizer->normalize($throwable);

        $this->assertInstanceOf(NormalizedThrowable::class, $result);
        $this->assertEquals($throwable->getMessage(), $result->getMessage());
        $this->assertEquals($throwable->getCode(), $result->getCode());
        $this->assertSame($throwable, $result->getModifiedThrowable());
    }
}
