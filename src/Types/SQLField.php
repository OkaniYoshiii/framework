<?php

declare(strict_types=1);

namespace OkaniYoshiii\Framework\Types;

use Exception;
use OkaniYoshiii\Framework\Enums\SQLFieldType;
use OkaniYoshiii\Framework\Helpers\StringHelper;
use OkaniYoshiii\Framework\Types\Primitive\SnakeCaseWord;

class SQLField
{
    private readonly SnakeCaseWord $name;
    private readonly SQLFieldType $type;
    private ?int $length = null;
    private readonly bool $isNullable;
    private bool $isPrimaryKey = false;
    private bool $isUnsigned = false;

    public function __construct(SnakeCaseWord $name, SQLFieldType $type, bool $isNullable)
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

    public function getName() : SnakeCaseWord
    {
        return $this->name;
    }

    public function getType() : SQLFieldType
    {
        return $this->type;
    }

    public function getIsNullable() : bool
    {
        return $this->isNullable;
    }

    public function getDatabaseMapping() : string
    {
        $name = $this->name->getValue();
        $type =  $this->getType()->mapping();
        $length = ($this->length !== null) ? '(' . $this->length . ')' : '';
        $isNullable = ($this->getIsNullable()) ? 'NULL' : 'NOT NULL';
        $isPrimaryKey = ($this->isPrimaryKey) ? 'PRIMARY KEY AUTO_INCREMENT' : '';
        $isUnsigned = ($this->isUnsigned) ? 'UNSIGNED' : '';

        return implode(' ', [$name, $type, $length, $isUnsigned, $isNullable, $isPrimaryKey]);
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }
}