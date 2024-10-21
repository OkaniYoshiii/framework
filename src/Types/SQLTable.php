<?php

namespace OkaniYoshiii\Framework\Types;

use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;
use OkaniYoshiii\Framework\Types\Primitive\Word;

class SQLTable
{
    private readonly SnakeCaseWord $name;
    private readonly SQLPrimaryKey $primaryKey;
    private readonly array $fields;

    public function __construct(SnakeCaseWord $name, SQLPrimaryKey $primaryKey, SQLField ...$fields)
    {
        $this->name = $name;
        $this->primaryKey = $primaryKey;
        $this->fields = $fields;
    }

    public function getName() : SnakeCaseWord
    {
        return $this->name;
    }

    public function getPrimarykey() : SQLPrimaryKey
    {
        return $this->primaryKey;
    }

    public function getFields() : array
    {
        return $this->fields;
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }
}