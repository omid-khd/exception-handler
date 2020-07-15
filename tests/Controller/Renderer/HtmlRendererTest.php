<?php

namespace Khadem\Tests\ExceptionHandler\Controller\Renderer;

use Khadem\ExceptionHandler\Controller\Renderer\HtmlRenderer;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class HtmlRendererTest
 */
final class HtmlRendererTest extends MockeryTestCase
{
    public function test_it_set_http_status_code_to_500_if_exception_has_no_valid_code()
    {
        $message = 'Custom Exception Message';
        $renderer = new HtmlRenderer();

        $expectedResponse = sprintf(
            '<!doctype><html><head></head><body><h1>%s</h1><p>%s</p></body></html>',
            'Internal Server Error',
            $message
        );

        $this->assertEquals($expectedResponse, $renderer->render(new \Exception($message)));
    }

    public function test_it_get_http_status_code_from_exception_code()
    {
        $message = 'Custom Exception Message';
        $renderer = new HtmlRenderer();

        $expectedResponse = sprintf(
            '<!doctype><html><head></head><body><h1>%s</h1><p>%s</p></body></html>',
            'Bad Request',
            $message
        );

        $this->assertEquals($expectedResponse, $renderer->render(new \Exception($message, 400)));
    }
}
