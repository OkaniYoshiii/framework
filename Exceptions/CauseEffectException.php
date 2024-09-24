<?php

namespace Framework\Exceptions;

use Exception;

class CauseEffectException extends Exception
{
    protected $message;
    protected string $consequence;
    protected string $cause;

    public function __construct(string $consequence, string $cause)
    {
        $this->consequence = $consequence;
        $this->cause = $cause;
        $this->message = $consequence . ' : ' . $cause;

        parent::__construct($this->message);
    }

    public function getCause() : string
    {
        return $this->cause;
    }

    public function getConsequence() : string
    {
        return $this->consequence;
    }
}