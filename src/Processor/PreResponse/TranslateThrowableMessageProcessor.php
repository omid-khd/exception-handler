<?php

namespace Khadem\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\Translation\TranslatableThrowableInterface;
use Khadem\ExceptionHandler\Translation\TranslatedThrowable;
use Khadem\ExceptionHandler\Translation\TranslatorInterface;

final class TranslateThrowableMessageProcessor implements PreResponseProcessorInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function preProcess(\Throwable $throwable): \Throwable
    {
        if ($throwable instanceof TranslatableThrowableInterface) {
            $throwable = new TranslatedThrowable($this->translator->translate($throwable), $throwable);
        }

        return $throwable;
    }
}