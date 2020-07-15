<?php

namespace Khadem\Tests\ExceptionHandler\Normalizer;

use Khadem\ExceptionHandler\Exception\UnexpectedCallableResultFormatException;
use Khadem\ExceptionHandler\Exception\UnexpectedMappingFormatException;
use Khadem\ExceptionHandler\Normalizer\MappedListThrowableNormalizer;
use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Khadem\ExceptionHandler\Normalizer\ThrowableNormalizerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class MappedListThrowableNormalizerTest
 */
final class MappedListThrowableNormalizerTest extends MockeryTestCase
{
    public function test_it_fallback_to_internal_server_error_normalizer()
    {
        $throwable  = new \Exception();
        $normalizer = new MappedListThrowableNormalizer();
        $result     = $normalizer->normalize($throwable);

        $this->assertInstanceOf(NormalizedThrowable::class, $result);
        $this->assertEquals('Internal Server Error', $result->getMessage());
        $this->assertEquals(500, $result->getCode());
        $this->assertSame($throwable, $result->getModifiedThrowable());
    }

    public function test_it_ask_fallback_normalizer_if_given_throwable_is_not_mapped()
    {
        $throwable  = new \Exception();

        $fallbackNormalizer = \Mockery::mock(ThrowableNormalizerInterface::class);
        $fallbackNormalizer->shouldReceive('normalize')
                           ->once()
                           ->with($throwable)
                           ->andReturn(new NormalizedThrowable('', 0, $throwable));

        $normalizer = new MappedListThrowableNormalizer([], $fallbackNormalizer);
        $result     = $normalizer->normalize($throwable);

        $this->assertInstanceOf(NormalizedThrowable::class, $result);
        $this->assertSame($throwable, $result->getModifiedThrowable());
    }

    public function test_it_throw_exception_if_mapping_factory_result_format_is_not_correct()
    {
        $throwable  = new \Exception();
        $normalizer = new MappedListThrowableNormalizer([\Exception::class => static function () {
            return new \stdClass();
        }]);

        $this->expectException(UnexpectedCallableResultFormatException::class);
        $this->expectExceptionMessage('Unexpected callable result. expected array got object');

        $normalizer->normalize($throwable);
    }

    public function test_it_throw_exception_if_mapping_has_unexpected_format()
    {
        $throwable  = new \Exception();
        $normalizer = new MappedListThrowableNormalizer([\Exception::class => new \stdClass()]);

        $this->expectException(UnexpectedMappingFormatException::class);
        $this->expectExceptionMessage("Unexpected mapping format. expected format is ['Exception' => ['custom message', 'custom code']]");

        $normalizer->normalize($throwable);
    }

    public function test_it_normalize_throwable()
    {
        $message    = 'Custom Message';
        $code       = 400;
        $throwable  = new \Exception();
        $normalizer = new MappedListThrowableNormalizer([\Exception::class => static function () use ($code, $message) {
            return [$code, $message];
        }]);

        $result = $normalizer->normalize($throwable);

        $this->assertInstanceOf(NormalizedThrowable::class, $result);
        $this->assertEquals($message, $result->getMessage());
        $this->assertEquals($code, $result->getCode());
        $this->assertSame($throwable, $result->getModifiedThrowable());
    }
}
