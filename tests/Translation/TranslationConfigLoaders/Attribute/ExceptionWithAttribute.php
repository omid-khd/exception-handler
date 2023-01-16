<?php

declare(strict_types=1);

namespace Tests\ExceptionHandler\Translation\TranslationConfigLoaders\Attribute;

use Exception;
use ExceptionHandler\Translation\TranslationConfigLoaders\Attribute\TranslationConfig;

#[TranslationConfig('trans_id')]
final class ExceptionWithAttribute extends Exception
{
}
