<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata;

use Throwable;

final class ExceptionMetadata
{
    private $code;
    private $message;
    private $throwable;

    public function __construct(int $code, string $message, Throwable $throwable)
    {
        $this->code = $code;
        $this->message = $message;
        $this->throwable = $throwable;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}
