<?php

declare(strict_types=1);

namespace ExceptionHandler\Http;

use Psr\Http\Message\MessageInterface;

interface HttpRequestProviderInterface
{
    public function getHttpRequest(): MessageInterface;
}
