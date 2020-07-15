<?php

namespace Khadem\ExceptionHandler\Normalizer;

use Khadem\ExceptionHandler\ModifiedThrowableInterface;

class NormalizedThrowable extends \Exception implements ModifiedThrowableInterface
{
    private $throwable;

    public function __construct(string $message, int $code, \Throwable $throwable)
    {
        parent::__construct($message, $code, $throwable);
    }

    public function getModifiedThrowable(): \Throwable
    {
        return $this->getPrevious();
    }
}