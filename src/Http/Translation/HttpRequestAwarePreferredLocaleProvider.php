<?php

declare(strict_types=1);

namespace ExceptionHandler\Http\Translation;

use ExceptionHandler\Http\HttpRequestProviderInterface;
use ExceptionHandler\Translation\Locale\PreferredLocaleProviderInterface;

final class HttpRequestAwarePreferredLocaleProvider implements PreferredLocaleProviderInterface
{
    public function __construct(private readonly HttpRequestProviderInterface $httpRequestProvider)
    {
    }

    public function getPreferredLocale(): ?string
    {
        $request = $this->httpRequestProvider->getHttpRequest();
        $preferredLocales = $request->getHeader('Accept-Language');

        return empty($preferredLocales) ? null : array_shift($preferredLocales);
    }
}
