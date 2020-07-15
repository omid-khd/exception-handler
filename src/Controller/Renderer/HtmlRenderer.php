<?php

namespace Khadem\ExceptionHandler\Controller\Renderer;

use Khadem\ExceptionHandler\Controller\ReasonPhraseTrait;

final class HtmlRenderer implements ControllerRendererInterface
{
    use ReasonPhraseTrait;

    public function render(\Throwable $throwable): string
    {
        $code         = $this->isReasonPhraseCode($throwable->getCode()) ? $throwable->getCode() : 500;
        $reasonPhrase = $this->getReasonPhrase($code);

        return sprintf(
            '<!doctype><html><head></head><body><h1>%s</h1>%s</body></html>',
            $reasonPhrase,
            $throwable->getMessage() !== $reasonPhrase ? "<p>{$throwable->getMessage()}</p>" : ''
        );
    }
}