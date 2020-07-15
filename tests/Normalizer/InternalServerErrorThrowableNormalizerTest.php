<?php

namespace Khadem\Tests\ExceptionHandler\Normalizer;

use Khadem\ExceptionHandler\Normalizer\InternalServerErrorThrowableNormalizer;
use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class InternalServerErrorThrowableNormalizerTest
 */
final class InternalServerErrorThrowableNormalizerTest extends MockeryTestCase
{
    public function test_it_normalize_throwable()
    {
        $throwable  = new \Exception();
        $normalizer = new InternalServerErrorThrowableNormalizer();
        $result     = $normalizer->normalize($throwable);

        $this->assertInstanceOf(NormalizedThrowable::class, $result);
        $this->assertEquals('Internal Server Error', $result->getMessage());
        $this->assertEquals(500, $result->getCode());
        $this->assertSame($throwable, $result->getModifiedThrowable());
    }
}
