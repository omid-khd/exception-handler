<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata\MetadataLoaders\MetadataAware;

use ExceptionHandler\Metadata\ExceptionMetadata;
use Throwable;

interface MetadataAwareExceptionInterface extends Throwable
{
    public function getMetadata(): ExceptionMetadata;
}