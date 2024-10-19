<?php

namespace OkaniYoshiii\Framework\Types\Primitive;

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

    abstract protected function validate(string $value) : void;

    public function getValue() : string
    {
        return $this->value;
    }
}