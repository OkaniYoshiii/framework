<?php

declare(strict_types=1);

namespace Framework\Types;

use Framework\Enums\TablePropertyType;

class TableProperty
{
    public function __construct(private readonly string $name, private readonly TablePropertyType $type, private readonly bool $isNullable)
    {
    }

    public function __toString()
    {
        $isNullable = ($this->isNullable) ? 'NULL' : 'NOT NULL';
        return $this->name . ' (' . $this->type->value . ') - ' . $isNullable;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getType() : TablePropertyType
    {
        return $this->type;
    }

    public function getIsNullable() : bool
    {
        return $this->isNullable;
    }
}