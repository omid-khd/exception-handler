<?php

namespace Khadem\ExceptionHandler\Controller;

class JsonEnvelop
{
    public function wrap(string $message, int $code, \Throwable $throwable)
    {
        return ['error' => compact('code', 'message')];
    }
}