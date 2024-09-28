<?php

declare(strict_types=1);

namespace Framework\Types;

use Exception;
use Framework\Enums\TablePropertyType;
use Framework\Helpers\StringHelper;

class TableProperty
{
    private readonly string $name;
    private readonly TablePropertyType $type;
    private ?int $length = null;
    private readonly bool $isNullable;
    private bool $isPrimaryKey = false;
    private bool $isUnsigned = false;
    public function __construct(string $name, TablePropertyType $type, bool $isNullable)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isNullable = $isNullable;
    }

    public function __toString()
    {
        return $this->getDatabaseMapping();
    }

    public function setLength(int $length) : self
    {
        if($this->type->maxLength() === null) throw new Exception('Length cannot be set on a property of type ' . $this->type->value . '(' . $this->type->mapping() . ')');
        if($length > $this->type->maxLength()) throw new Exception('Length cannot be higher than the maximum length for this type of property. Max length : ' . $this->type->maxLength());

        $this->length = $length;

        return $this;
    }

    public function setIsPrimaryKey(bool $isPrimaryKey) : self
    {
        $this->isPrimaryKey = $isPrimaryKey;

        return $this;
    }

    public function setIsUnsigned(bool $isUnsigned) : self
    {
        $this->isUnsigned = $isUnsigned;

        return $this;
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

    public function getDatabaseMapping() : string
    {
        $name = StringHelper::camelCaseToSnakeCase($this->getName());
        $type =  $this->getType()->mapping();
        $length = ($this->length !== null) ? '(' . $this->length . ')' : '';
        $isNullable = ($this->getIsNullable()) ? 'NULL' : 'NOT NULL';
        $isPrimaryKey = ($this->isPrimaryKey) ? 'PRIMARY KEY AUTO_INCREMENT' : '';
        $isUnsigned = ($this->isUnsigned) ? 'UNSIGNED' : '';

        return implode(' ', [$name, $type, $length, $isUnsigned, $isNullable, $isPrimaryKey]);
    }
}