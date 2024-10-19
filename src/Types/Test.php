<?php

namespace OkaniYoshiii\Framework\Types;

use Closure;
use Exception;

class Test
{
    private readonly string $message;
    private ?Exception $exception;
    private bool $isValidated;

    public function __construct(string $message, callable $test)
    {
        $this->message = $message;

        try {
            ($test)();

            $this->isValidated = true;
            $this->exception = null;

        } catch(Exception $exception) {

            $this->isValidated = false;
            $this->exception = $exception;

        }
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function getException() : ?Exception
    {
        return $this->exception;
    }

    public function isValidated() : bool
    {
        return $this->isValidated;
    }
}