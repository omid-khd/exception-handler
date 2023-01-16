<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\TranslationConfigLoaders\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class TranslationConfig
{
    public function __construct(
        public string $id,
        public array $parameters = [],
        public ?string $domain = null,
        public ?string $locale = null,
    ) {
    }
}
