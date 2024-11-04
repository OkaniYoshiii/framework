<?php

namespace OkaniYoshiii\Framework\Contracts\Attributes;

use Attribute;
use OkaniYoshiii\Framework\Enums\DataType;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SQLField
{
    public function __construct(
        private string $name,
        private bool $isNullable,
        private DataType $type
    ){}

    public function getName() : string
    {
        return $this->name;
    }

    public function getIsNullable() : bool
    {
        return $this->isNullable;
    }

    public function getType() : DataType
    {
        return $this->type;
    }
}