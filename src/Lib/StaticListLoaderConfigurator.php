<?php

declare(strict_types=1);

namespace ExceptionHandler\Lib;

use InvalidArgumentException;
use Throwable;
use Webmozart\Assert\Assert;

class StaticListLoaderConfigurator
{
    /**
     * @param array<class-string, callable(Throwable $e): mixed>|string $list
     */
    final public function __construct(private array|string $list)
    {
    }

    public function configure(StaticList $loader): void
    {
        $list = $this->getList();

        $loader->setList($list);
    }

    /**
     * @return array<class-string, callable(Throwable $e): mixed>
     */
    private function getList(): array
    {
        if (is_array($this->list)) {
            return $this->list;
        }

        if (!is_file($this->list)) {
            throw new InvalidArgumentException("File {$this->list} does not exist");
        }

        if (pathinfo($this->list, PATHINFO_EXTENSION) !== 'php') {
            throw new InvalidArgumentException("File {$this->list} is not a php file");
        }

        $list = include $this->list;

        Assert::isArray($list);

        return $this->list = $list;
    }
}
