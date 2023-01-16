<?php

declare(strict_types=1);

namespace ExceptionHandler\Metadata;

use Throwable;

class ExceptionMetadataLoader
{
    private iterable $metadataLoaders;

    public function __construct(iterable $metadataLoaders = [])
    {
        $this->metadataLoaders = $metadataLoaders;
    }

    public function loadMetadata(Throwable $e): ExceptionMetadata
    {
        foreach ($this->metadataLoaders as $metadataLoader) {
            assert($metadataLoader instanceof MetadataLoaderInterface);

            if ($metadataLoader->supports($e)) {
                return $metadataLoader->load($e);
            }
        }

        return new ExceptionMetadata(500, 'Internal Server Error', $e);
    }
}
