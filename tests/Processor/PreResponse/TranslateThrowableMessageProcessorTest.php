<?php

namespace Khadem\Tests\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\Processor\PreResponse\TranslateThrowableMessageProcessor;
use Khadem\ExceptionHandler\Translation\TranslatableThrowableInterface;
use Khadem\ExceptionHandler\Translation\TranslatedThrowable;
use Khadem\ExceptionHandler\Translation\TranslatorInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class TranslateThrowableMessageProcessorTest
 */
final class TranslateThrowableMessageProcessorTest extends MockeryTestCase
{
    public function test_it_dont_translate_if_given_throwable_is_not_instance_of_translatable_throwable_interface()
    {
        $translator = \Mockery::mock(TranslatorInterface::class);
        $translator->shouldNotReceive('translate');

        $translateThrowableMessageProcessor = new TranslateThrowableMessageProcessor($translator);

        $throwable = new \Exception();

        $result = $translateThrowableMessageProcessor->preProcess($throwable);

        $this->assertSame($throwable, $result);
    }

    public function test_it_dont_translate_throwable()
    {
        $throwable = new TranslatableThrowable();
        $message   = 'Translated Message';

        $translator = \Mockery::mock(TranslatorInterface::class);
        $translator->shouldReceive('translate')
                   ->once()
                   ->with($throwable)
                   ->andReturn($message);

        $translateThrowableMessageProcessor = new TranslateThrowableMessageProcessor($translator);

        $result = $translateThrowableMessageProcessor->preProcess($throwable);

        $this->assertInstanceOf(TranslatedThrowable::class, $result);
        $this->assertEquals($message, $result->getMessage());
        $this->assertSame($throwable, $result->getModifiedThrowable());
    }
}

class TranslatableThrowable extends \Exception implements TranslatableThrowableInterface
{
    public function getMessageKey(): string
    {
    }

    public function getMessageData()
    {
    }

}
