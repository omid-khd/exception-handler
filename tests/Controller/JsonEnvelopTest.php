<?php

namespace Khadem\Tests\ExceptionHandler\Controller;

use Khadem\ExceptionHandler\Controller\JsonEnvelop;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class JsonEnvelopTest
 */
final class JsonEnvelopTest extends MockeryTestCase
{
    public function test_it_wrap_code_and_message_in_error_key()
    {
        $code = 400;
        $message = 'Bad Request';
        $e = new \Exception($message, $code);
        $envelop = new JsonEnvelop();
        $result = $envelop->wrap($message, $code, $e);

        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('code', $result['error']);
        $this->assertEquals($code, $result['error']['code']);
        $this->assertArrayHasKey('message', $result['error']);
        $this->assertEquals($message, $result['error']['message']);
    }
}
