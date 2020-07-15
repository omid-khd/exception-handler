<?php

namespace Khadem\ExceptionHandler\Exception;

final class UnexpectedCallableResultFormatException extends \UnexpectedValueException
{
    public static function fromResult($result): self
    {
        switch (true) {
            case !is_array($result):
                $message = 'Unexpected callable result. expected array got ' . gettype($result);
                break;
            case 2 !== count($result):
                $message = "Unexpected callable result format. expected format is ['custom message', 'custom code']";
                break;
            case !is_int($result[0]):
                $message = 'Unexpected callable result format. expected first item of result to be of type integer got ' . gettype($result[1]);
                break;
            case !is_string($result[1]):
                $message = 'Unexpected callable result format. expected second item of result to be of type string got ' . gettype($result[0]);
                break;
            default:
                $message = 'Unexpected callable result format.';
        }

        return new static($message);
    }
}