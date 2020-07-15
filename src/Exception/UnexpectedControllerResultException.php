<?php

namespace Khadem\ExceptionHandler\Exception;

use Khadem\ExceptionHandler\AssertHelper;
use Psr\Http\Message\ResponseInterface;

final class UnexpectedControllerResultException extends \UnexpectedValueException
{
    public static function fromResult($result): self
    {
        $message = sprintf(
            'The controller must return a "%s" object but it returned %s.',
            ResponseInterface::class,
            AssertHelper::determineType($result)
        );

        return new static($message);
    }
}