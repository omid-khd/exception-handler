<?php

namespace Khadem\ExceptionHandler\Processor\PreResponse;

use Khadem\ExceptionHandler\ClassHierarchyTrait;
use Khadem\ExceptionHandler\AccessModifiedThrowableTrait;
use Khadem\ExceptionHandler\Reporter\ExceptionReporterInterface;

final class ReportThrowableProcessor implements PreResponseProcessorInterface
{
    use ClassHierarchyTrait;
    use AccessModifiedThrowableTrait;

    private $reporter;

    private $unreportableThrowables;

    public function __construct(ExceptionReporterInterface $reporter, array $unreportableThrowables = [])
    {
        $this->reporter               = $reporter;
        $this->unreportableThrowables = array_flip(array_map(static function ($unreportable) {
            if (is_string($unreportable) && class_exists($unreportable)) {
                return $unreportable;
            }

            if (!is_string($unreportable)) {
                throw new \InvalidArgumentException(sprintf('Expected string got %s.', gettype($unreportable)));
            }

            throw new \InvalidArgumentException(sprintf('Class with FQN %s not found.', $unreportable));
        }, $unreportableThrowables));
    }

    public function preProcess(\Throwable $throwable): \Throwable
    {
        $reportable = $this->getModifiedThrowable($throwable);

        foreach ($this->getClassHierarchy($reportable) as $class) {
            if (isset($this->unreportableThrowables[$class])) {
                return $throwable;
            }
        }

        $this->reporter->report($reportable);

        return $throwable;
    }
}