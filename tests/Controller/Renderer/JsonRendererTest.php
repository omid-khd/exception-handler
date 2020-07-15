<?php

namespace Khadem\Tests\ExceptionHandler\Controller\Renderer;

use Khadem\ExceptionHandler\Controller\JsonEnvelop;
use Khadem\ExceptionHandler\Controller\Renderer\JsonRenderer;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class JsonRendererTest
 */
final class JsonRendererTest extends MockeryTestCase
{
    public function test_it_json_encode_throwable_message_and_code()
    {
        $customMessage = 'Custom Exception Message';
        $statusCode    = 400;
        $renderer      = new JsonRenderer();
        $result        = json_decode($renderer->render(new \Exception($customMessage, $statusCode)), true);

        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('code', $result['error']);
        $this->assertArrayHasKey('message', $result['error']);
    }

    public function test_it_customize_envelop_with_given_json_envelop()
    {
        $customMessage = 'Custom Exception Message';
        $statusCode    = 400;
        $renderer      = new JsonRenderer(new class extends JsonEnvelop {
            public function wrap(string $message, int $code, \Throwable $throwable)
            {
                return [
                    'custom_message_key' => $message,
                    'custom_code_key'    => $code,
                ];
            }
        });

        $result = json_decode($renderer->render(new \Exception($customMessage, $statusCode)), true);

        $this->assertArrayHasKey('custom_message_key', $result);
        $this->assertArrayHasKey('custom_code_key', $result);
    }
}
