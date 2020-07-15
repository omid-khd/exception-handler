<?php

namespace Khadem\ExceptionHandler\Controller\Renderer;

interface ControllerRendererInterface
{
    public function render(\Throwable $throwable): string;
}