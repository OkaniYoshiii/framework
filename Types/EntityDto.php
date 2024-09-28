<?php

namespace Framework\Types;

use Framework\Helpers\StringHelper;

class EntityDto
{
    private readonly string $name;
    private readonly string $table;
    private readonly string $primaryKey;
    private readonly array $properties;

    public function __construct(string $name, string $primaryKey, array ...$properties)
    {
        $this->name = $name;
        $this->table = StringHelper::camelCaseToSnakeCase($name);
        $this->primaryKey = $primaryKey;
        $this->properties = $properties;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getTable() : string
    {
        return $this->table;
    }

    public function getPrimarykey() : string
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