<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class ThrowableMetadata
{
    public function __construct(public int $code, public string $message)
    {
    }
}
