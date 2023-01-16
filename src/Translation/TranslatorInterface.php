<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation;

interface TranslatorInterface
{
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string;
}