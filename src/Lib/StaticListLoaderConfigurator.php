<?php

declare(strict_types=1);

namespace ExceptionHandler\Lib;

use InvalidArgumentException;

final class StaticListLoaderConfigurator
{
    /**
     * @var array|string
     */
    private $list;

    public function __construct($list)
    {
        $this->list = $list;
    }

    public function configure(StaticListLoader $loader): void
    {
        $list = $this->getList();

        $loader->setList($list);
    }

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

        return $this->list = include $this->list;
    }
}
