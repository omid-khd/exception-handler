<?php

namespace Khadem\Tests\ExceptionHandler\Controller;

use Khadem\ExceptionHandler\Controller\ReasonPhraseTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class ReasonPhraseTraitTest
 */
final class ReasonPhraseTraitTest extends MockeryTestCase
{
    use ReasonPhraseTrait;

    /**
     * @dataProvider reasonPhraseDataProvider
     */
    public function test_it_get_reason_phrase($code, $reasonPhrase)
    {
        $this->assertEquals($reasonPhrase, $this->getReasonPhrase($code));
    }

    /**
     * @dataProvider isReasonPhraseDataProvider
     */
    public function test_it_check_if_code_is_a_http_reason_phrase($code, $isReasonPhrase)
    {
        $this->assertEquals($isReasonPhrase, $this->isReasonPhraseCode($code));
    }

    public function reasonPhraseDataProvider()
    {
        foreach ($this->phrases as $code => $reasonPhrase) {
            yield [$code, $reasonPhrase];
        }

        yield [0, $this->phrases[500]];
    }

    public function isReasonPhraseDataProvider()
    {
        foreach ($this->phrases as $code => $reasonPhrase) {
            yield [$code, true];
        }

        yield [0, false];
    }
}
