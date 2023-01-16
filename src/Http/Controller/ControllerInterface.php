<?php

declare(strict_types=1);

namespace ExceptionHandler\Http\Controller;

use ExceptionHandler\Metadata\ExceptionMetadata;
use Psr\Http\Message\MessageInterface;

interface ControllerInterface
{
    public function __invoke(MessageInterface $request, ExceptionMetadata $metadata): MessageInterface;
}