<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Metadata\MetadataLoaders\Attribute;

use Exception;
use ExceptionHandler\Metadata\MetadataLoaders\Attribute\ThrowableMetadata;

#[ThrowableMetadata(500, 'Error Message')]
final class ExceptionWithAttribute extends Exception
{
}
