<?php

namespace OkaniYoshiii\Framework;

abstract class StringType
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    abstract protected function validate() : void;

    public function getValue() : string
    {
        return $this->value;
    }
}