<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata;

use Throwable;
use Webmozart\Assert\Assert;

class ExceptionMetadataLoader
{
    /**
     * @param iterable<MetadataLoaderInterface> $metadataLoaders
     */
    public function __construct(private readonly iterable $metadataLoaders = [])
    {
    }

    public function loadMetadata(Throwable $e): ExceptionMetadata
    {
        foreach ($this->metadataLoaders as $metadataLoader) {
            Assert::isInstanceOf($metadataLoader, MetadataLoaderInterface::class);

            if ($metadataLoader->support($e)) {
                return $metadataLoader->load($e);
            }
        }

        return new ExceptionMetadata(500, 'Internal Server Error', $e);
    }
}
