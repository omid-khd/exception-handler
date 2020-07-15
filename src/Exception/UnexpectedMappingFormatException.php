<?php

namespace Khadem\ExceptionHandler\Exception;

final class UnexpectedMappingFormatException extends \InvalidArgumentException
{
    public static function forClass(string $class): self
    {
        $message = "Unexpected mapping format. expected format is ['{$class}' => ['custom message', 'custom code']]";

        return new static($message);
    }
}