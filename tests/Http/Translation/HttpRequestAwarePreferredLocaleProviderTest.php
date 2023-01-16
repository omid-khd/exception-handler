<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Http\Translation;

use ExceptionHandler\Http\HttpRequestProviderInterface;
use ExceptionHandler\Http\Translation\HttpRequestAwarePreferredLocaleProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;

final class HttpRequestAwarePreferredLocaleProviderTest extends TestCase
{
    public function testItReturnNullIfAcceptLanguageHeaderContainsNoValue(): void
    {
        $request = $this->createMock(MessageInterface::class);
        $request->expects($this->once())->method('getHeader')->willReturn([]);

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $provider = new HttpRequestAwarePreferredLocaleProvider($httpRequestProvider);

        $this->assertNull($provider->getPreferredLocale());
    }

    public function testItReturnPreferredLanguageSpecifiedByRequestAcceptHeader(): void
    {
        $request = $this->createMock(MessageInterface::class);
        $request->expects($this->once())->method('getHeader')->willReturn(['en_EN']);

        $httpRequestProvider = $this->createMock(HttpRequestProviderInterface::class);
        $httpRequestProvider->expects($this->once())->method('getHttpRequest')->willReturn($request);

        $provider = new HttpRequestAwarePreferredLocaleProvider($httpRequestProvider);

        $this->assertEquals('en_EN', $provider->getPreferredLocale());
    }
}
