<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\MetadataAware;

use ExceptionHandler\Metadata\ExceptionMetadata;

interface MetadataAwareExceptionInterface
{
    public function getMetadata(): ExceptionMetadata;
}