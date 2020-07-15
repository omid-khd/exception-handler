<?php

namespace Khadem\Tests\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\Processor\PreResponse\ReportThrowableProcessor;
use Khadem\ExceptionHandler\Reporter\ExceptionReporterInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class ReportThrowableProcessorTest
 */
final class ReportThrowableProcessorTest extends MockeryTestCase
{
    public function test_it_throw_exception_if_unreportable_throwable_constructor_argument_is_not_string()
    {
        $reporter = \Mockery::mock(ExceptionReporterInterface::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected string got object');

        new ReportThrowableProcessor($reporter, [new \stdClass()]);
    }

    public function test_it_throw_exception_if_unreportable_throwable_constructor_argument_is_not_a_class()
    {
        $reporter = \Mockery::mock(ExceptionReporterInterface::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Class with FQN foo not found.');

        new ReportThrowableProcessor($reporter, ['foo']);
    }

    public function test_it_dont_report_throwable()
    {
        $reporter = \Mockery::mock(ExceptionReporterInterface::class);
        $reporter->shouldNotReceive('report');

        $reportThrowableProcessor = new ReportThrowableProcessor($reporter, [\Exception::class]);

        $throwable = new \InvalidArgumentException();

        $result = $reportThrowableProcessor->preProcess($throwable);

        $this->assertSame($throwable, $result);
    }

    public function test_it_report_throwable()
    {
        $throwable = new \InvalidArgumentException();

        $reporter = \Mockery::mock(ExceptionReporterInterface::class);
        $reporter->shouldReceive('report')->once()->with($throwable)->andReturn();

        $reportThrowableProcessor = new ReportThrowableProcessor($reporter, []);

        $result = $reportThrowableProcessor->preProcess($throwable);

        $this->assertSame($throwable, $result);
    }
}
