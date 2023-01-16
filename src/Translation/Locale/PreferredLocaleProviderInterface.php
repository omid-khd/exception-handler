<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation\Locale;

interface PreferredLocaleProviderInterface
{
    public function getPreferredLocale(): ?string;
}