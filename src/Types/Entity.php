<?php

namespace OkaniYoshiii\Framework\Types;

use OkaniYoshiii\Framework\Types\Primitive\PascalCaseWord;

class Entity
{
    private readonly PascalCaseWord $name;
    private readonly array $properties;

    public function __construct(PascalCaseWord $name, SQLField ...$properties)
    {
        $this->name = $name;
        $this->properties = $properties;
    }

    public function getName() : PascalCaseWord
    {
        return $this->name;
    }

    public function getProperties() : array
    {
        return $this->properties;
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }
}