<?php

namespace Khadem\ExceptionHandler\Processor\PostResponse;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface PostResponseProcessorInterface
{
    public function postProcess(ResponseInterface $response, RequestInterface $request): ResponseInterface;
}