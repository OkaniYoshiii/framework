<?php

namespace OkaniYoshiii\Framework\Types;

use OkaniYoshiii\Framework\Types\Primitive\Word;

class SQLTable
{
    private readonly Word $name;
    private readonly Word $primaryKey;
    private readonly array $properties;

    public function __construct(Word $name, Word $primaryKey, array ...$properties)
    {
        $this->name = $name;
        $this->primaryKey = $primaryKey;
        $this->properties = $properties;
    }

    public function getName() : Word
    {
        return $this->name;
    }

    public function getPrimarykey() : Word
    {
        return $this->primaryKey;
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