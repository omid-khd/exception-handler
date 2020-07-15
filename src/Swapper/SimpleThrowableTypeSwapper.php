<?php

namespace Khadem\ExceptionHandler\Swapper;

use Khadem\ExceptionHandler\AssertHelper;

final class SimpleThrowableTypeSwapper
{
    private $input;

    private $output;

    public function __construct(string $input, string $output)
    {
        $this->validateInput($input);
        $this->validateOutput($output);

        $this->input  = $input;
        $this->output = $output;
    }

    public function __invoke(\Throwable $throwable): \Throwable
    {
        if (get_class($throwable) !== $this->input) {
            $message = sprintf('Expected instance of %s as input but throwable is of type %s', $this->input, get_class($throwable));

            throw new \InvalidArgumentException($message);
        }

        $to = $this->output;

        return new $to($throwable->getMessage(), $throwable->getCode(), $throwable);
    }

    private function validateInput(string $input): void
    {
        if (!class_exists($input)) {
            throw new \InvalidArgumentException(sprintf('Class with FQN %s not found.', $input));
        }

        if (!is_subclass_of($input, \Throwable::class)) {
            throw new \InvalidArgumentException(
                sprintf('Expected instance of %s got %s', \Throwable::class, $input)
            );
        }
    }

    private function validateOutput(string $output): void
    {
        if (!class_exists($output)) {
            throw new \InvalidArgumentException(sprintf('Class with FQN %s not found.', $output));
        }

        if (!is_subclass_of($output, \Throwable::class)) {
            throw new \InvalidArgumentException(
                sprintf('Expected instance of %s got %s', \Throwable::class, $output)
            );
        }
    }
}