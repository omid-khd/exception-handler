<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata;

use Throwable;

final readonly class ExceptionMetadata
{
    public function __construct(public int $code, public string $message, public Throwable $throwable)
    {
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
