<?php

namespace Khadem\ExceptionHandler\Controller;

use Khadem\ExceptionHandler\Controller\Renderer\ControllerRendererInterface;
use Khadem\ExceptionHandler\Normalizer\NormalizedThrowable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class Controller
{
    use ReasonPhraseTrait;

    private $responseFactory;

    private $streamFactory;

    private $renderer;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        ControllerRendererInterface $renderer
    ) {
        $this->responseFactory = $responseFactory;
        $this->streamFactory   = $streamFactory;
        $this->renderer        = $renderer;
    }

    public function __invoke(\Throwable $throwable, RequestInterface $request): ResponseInterface
    {
        $code         = $this->isReasonPhraseCode($throwable->getCode()) ? $throwable->getCode() : 500;
        $reasonPhrase = $this->getReasonPhrase($code);
        $content      = $this->renderer->render($throwable);
        $body         = $this->streamFactory->createStream($content);

        return $this->responseFactory->createResponse($code, $reasonPhrase)->withBody($body);
    }
}