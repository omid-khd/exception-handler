<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class ThrowableMetadata
{
    public int $code;
    public string $message;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}
