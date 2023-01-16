<?php

declare(strict_types=1);

namespace ExceptionHandler\Translation;

class TranslationConfig
{
    public string $id;
    public array $parameters = [];
    public ?string $domain = null;
    public ?string $locale = null;

    public function __construct(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null)
    {
        $this->id = $id;
        $this->parameters = $parameters;
        $this->domain = $domain;
        $this->locale = $locale;
    }
}
