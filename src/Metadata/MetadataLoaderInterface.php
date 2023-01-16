<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata;

use Throwable;

interface MetadataLoaderInterface
{
    public function support(Throwable $e): bool;

    public function load(Throwable $e): ExceptionMetadata;
}