<?php

declare(strict_types=1);

namespace ExceptionHandler\Http\Translation;

use ExceptionHandler\Http\HttpRequestProviderInterface;
use ExceptionHandler\Translation\Locale\PreferredLocaleProviderInterface;

final class HttpRequestAwarePreferredLocaleProvider implements PreferredLocaleProviderInterface
{
    private HttpRequestProviderInterface $httpRequestProvider;

    public function __construct(HttpRequestProviderInterface $httpRequestProvider)
    {
        $this->httpRequestProvider = $httpRequestProvider;
    }

    public function getPreferredLocale(): ?string
    {
        $request = $this->httpRequestProvider->getHttpRequest();
        $preferredLocales = $request->getHeader('Accept-Language');

        return empty($preferredLocales) ? null : array_shift($preferredLocales);
    }
}
