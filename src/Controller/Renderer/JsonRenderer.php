<?php

namespace Khadem\ExceptionHandler\Controller\Renderer;

use Khadem\ExceptionHandler\Controller\JsonEnvelop;

final class JsonRenderer implements ControllerRendererInterface
{
    private $envelop;

    public function __construct(JsonEnvelop $envelop = null)
    {
        $this->envelop = $envelop ?? new JsonEnvelop();
    }

    public function render(\Throwable $throwable): string
    {
        return json_encode($this->envelop->wrap(
            $throwable->getMessage(),
            $throwable->getCode(),
            $throwable
        ));
    }
}