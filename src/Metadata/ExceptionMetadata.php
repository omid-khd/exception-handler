<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata;

use Throwable;

final readonly class ExceptionMetadata
{
    public function __construct(
        private int $code,
        private string $message,
        private Throwable $throwable,
    ) {
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
