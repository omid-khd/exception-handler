<?php

namespace Khadem\ExceptionHandler\Exception;

use Psr\Http\Message\ResponseInterface;

final class UnexpectedResponseProcessorResultException extends \UnexpectedValueException
{
    public static function fromResult($result): self
    {
        $message = sprintf(
            'The response processor must return a "%s" object but it returned %s.',
            ResponseInterface::class,
            is_object($result) ? ('an instance of ' . get_class($result) . ' class') : gettype($result)
        );

        return new static($message);
    }
}